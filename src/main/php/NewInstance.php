<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;

use ArrayIterator;
use CallbackFilterIterator;
use InvalidArgumentException;
use Iterator;
use ParseError;
use ReflectionClass;
use ReflectionMethod;
use Traversable;

/**
 * Allows to create new instances of given class or interface.
 */
class NewInstance
{
    /**
     * map of already evaluated classes
     *
     * @var array<class-string,ReflectionClass>
     */
    private static array $classes = [];

    /**
     * returns a new instance of the given class or interface
     *
     * This instance is created with calling the constructor of the target
     * class. Method calls for non-mapped methods will be forwared to the method
     * of the target class.
     *
     * @api
     * @template T of object
     * @param  class-string<T>|T $target          interface or class to create a new instance of
     * @param  mixed[]           $constructorArgs optional  list of arguments for the constructor
     * @return T&ClassProxy
     */
    public static function of(
        string|object $target,
        array $constructorArgs = []
    ): ClassProxy {
        return self::callMapClass($target)->newInstanceArgs($constructorArgs);
    }

    /**
     * returns a new stub of the given class or interface
     *
     * The instance is created without calling the constructor of the target
     * class. In contrast to instances returned by of(), method calls are not
     * passed to the target method if no mapping exists, but return null.
     *
     * @api
     * @template T of object
     * @param  class-string<T>|T $target interface or class to create a new instance of
     * @return T&ClassProxy
     */
    public static function stub(string|object $target): ClassProxy
    {
        $proxy = self::callMapClass($target)->newInstanceWithoutConstructor();
        $proxy->preventParentCalls();
        return $proxy;
    }

    /**
     * returns the class name of any new instance for given target class or interface
     *
     * @api
     * @template T of object
     * @param   class-string<T>|T  $target
     * @return  class-string<T&ClassProxy>
     * @since   0.2.0
     */
    public static function classname(string|object $target): string
    {
        return self::callMapClass($target)->getName();
    }

    /**
     * returns the proxy class for given target class or interface
     *
     * @template T of object
     * @param   class-string<T>|T  $target
     * @return  ReflectionClass<T&ClassProxy>
     */
    private static function callMapClass(string|object $target): ReflectionClass
    {
        if (is_object($target)) {
            $target = \get_class($target);
        }

        $class = new ReflectionClass($target);
        $className = $class->getName();
        if (!isset(self::$classes[$className])) {
            self::$classes[$className] = self::forkCallMapClass($class);
        }

        return self::$classes[$className];
    }

    /**
     * reference to compile function
     *
     * @var  callable
     * @internal
     */
    public static $compile = __NAMESPACE__ . '\compile';

    /**
     * creates a new class from the given class which uses the CallMap trait
     *
     * @template T of object
     * @param  ReflectionClass<T> $class
     * @return ReflectionClass<T&ClassProxy>
     * @throws ProxyCreationFailure
     */
    private static function forkCallMapClass(
        ReflectionClass $class
    ): ReflectionClass {
        if ($class->isTrait()) {
            $class = self::forkTrait($class);
        }

        try {
            $compile = self::$compile;
            $compile(self::createCallmapProxyCode($class));
        } catch (\ParseError $pe) {
            throw new ProxyCreationFailure(
                'Failure while creating CallMap instance of '
                . $class->getName() . ': ' . $pe->getMessage(),
                $pe
            );
        }

        $classProxy = $class->getName() . 'CallMapProxy';
        /** @var class-string<T&ClassProxy> $classProxy */
        return new ReflectionClass($classProxy);
    }

    /**
     * create an intermediate class for the trait so that any methods of the
     * trait become callable as parent
     *
     * @template T of object
     * @param   ReflectionClass<T> $class
     * @return  ReflectionClass<T>
     * @throws  ProxyCreationFailure
     */
    private static function forkTrait(ReflectionClass $class): ReflectionClass
    {
        $code = sprintf(
            "abstract class %sCallMapFork {\n"
            . "    use \%s;\n}",
            $class->getShortName(),
            $class->getName()
        );
        if ($class->inNamespace()) {
            $code = sprintf(
                "namespace %s {\n%s}\n",
                $class->getNamespaceName(),
                $code
            );
        }

        try {
            $compile = self::$compile;
            $compile($code);
        } catch (ParseError $pe) {
            throw new ProxyCreationFailure(
                'Failure while creating forked trait instance of '
                . $class->getName() . ': ' . $pe->getMessage(),
                $pe
            );
        }

        $traitProxy = $class->getName() . 'CallMapFork';
        /** @var  class-string<ClassProxy&T> $traitProxy */
        return new \ReflectionClass($traitProxy);
    }

    /**
     * creates code for new class
     *
     * @template T of object
     * @param  ReflectionClass<T> $class
     * @return string
     * @throws InvalidArgumentException
     */
    private static function createCallmapProxyCode(
        ReflectionClass $class
    ): string {
        if ($class->isFinal()) {
            throw new InvalidArgumentException(
                'Can not create mapping proxy for final class '
                . $class->getName()
            );
        }

        $code = self::createClassDefinition($class)
            . self::createMethods($class)
            . "}\n";
        if ($class->inNamespace()) {
            return sprintf(
                "namespace %s {\n%s}\n",
                $class->getNamespaceName(),
                $code
            );
        }

        return $code;
    }

    /**
     * creates class definition for the proxy
     *
     * @template T of object
     * @param  ReflectionClass<T> $class
     * @return string
     */
    private static function createClassDefinition(ReflectionClass $class): string
    {
        return sprintf(
            "class %sCallMapProxy %s \\%s%s\bovigo\callmap\ClassProxy {\n"
            . "    use \bovigo\callmap\ClassProxyMethodHandler;\n",
            $class->getShortName(),
            $class->isInterface() ? 'implements ' : 'extends ',
            $class->getName(),
            $class->isInterface() ? ', ' : ' implements '
        );
    }

    /**
     * creates methods for the proxy
     *
     * @template T of object
     * @param  ReflectionClass<T>  $class
     * @return string
     */
    private static function createMethods(ReflectionClass $class): string
    {
        $code    = '';
        $methods = [];
        $params  = [];
        $voidMethods = [];
        foreach (self::methodsOf($class) as $method) {
            $return = true;
            $returnType = determineReturnTypeOf($method, $class);
            if (in_array($returnType, [': void', ': never'])) {
                $voidMethods[$method->getName()] = $method->getName();
                $return = false;
            }

            $param = paramsOf($method, $class);
            /* @var $method \ReflectionMethod */
            $code .= sprintf(
                "    #[\Override]"
                . "    %s function %s(%s)%s {\n"
                . "        %s\$this->handleMethodCall('%s', func_get_args(), %s);\n"
                . "    }\n",
                ($method->isPublic() ? 'public' : 'protected'),
                $method->getName(),
                $param['string'],
                $returnType,
                $return ? 'return ' : '',
                $method->getName(),
                self::shouldReturnSelf($class, $method) ? 'true' : 'false'
            );
            $methods[] = "'" . $method->getName() . "' => '" . $method->getName() . "'";
            $params[$method->getName()] = $param['names'];
        }
        return $code . sprintf(
            "\n    private \$_allowedMethods = [%s];\n",
            join(', ', $methods)
        ) . sprintf(
            "\n    private \$_methodParams = %s;\n",
            var_export($params, true)
        ) . sprintf(
            "\n    private \$_voidMethods = %s;\n",
            var_export($voidMethods, true)
        );
    }

    /**
     * returns applicable methods for given class
     *
     * @template T of object
     * @param  ReflectionClass<T>  $class
     * @return Iterator<ReflectionMethod>
     */
    private static function methodsOf(ReflectionClass $class): Iterator
    {
        return new CallbackFilterIterator(
            new ArrayIterator($class->getMethods()),
            fn(ReflectionMethod $method) =>
                !$method->isPrivate()
                && !$method->isFinal()
                && !$method->isStatic()
                && !$method->isConstructor()
                && !$method->isDestructor()
        );
    }

    /**
     * detects whether a method should return the instance or null
     *
     * @template T of object
     * @param  ReflectionClass<T> $class
     * @param  ReflectionMethod   $method
     * @return bool
     */
    private static function shouldReturnSelf(
        ReflectionClass $class,
        ReflectionMethod $method
    ): bool {
        $returnType = ReturnType::detect($method);
        if (null === $returnType) {
            return false;
        }

        if ($returnType->isSelf() || $returnType->represents($class)) {
            return true;
        }

        foreach ($class->getInterfaces() as $interface) {
            if ($interface->getName() !== Traversable::class && $returnType->represents($interface)) {
                return true;
            }
        }

        while ($parent = $class->getParentClass()) {
            if ($returnType->represents($parent)) {
                return true;
            }

            $class = $parent;
        }

        return false;
    }
}
