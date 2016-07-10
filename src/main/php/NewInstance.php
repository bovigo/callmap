<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
/**
 * Allows to create new instances of given class or interface.
 */
class NewInstance
{
    /**
     * map of already evaluated classes
     *
     * @type  \ReflectionClass[]
     */
    private static $classes = [];

    /**
     * returns a new instance of the given class or interface
     *
     * This instance is created with calling the constructor of the target
     * class. Method calls for non-mapped methods will be forwared to the method
     * of the target class.
     *
     * @api
     * @param   string|object  $target           interface or class to create a new instance of
     * @param   mixed[]        $constructorArgs  optional  list of arguments for the constructor
     * @return  \bovigo\callmap\Proxy
     */
    public static function of($target, array $constructorArgs = []): Proxy
    {
        return self::callMapClass($target)
                ->newInstanceArgs($constructorArgs);
    }

    /**
     * returns a new stub of the given class or interface
     *
     * The instance is created without calling the constructor of the target
     * class. In contrast to instances returned by of(), method calls are not
     * passed to the target method if no mapping exists, but return null.
     *
     * @api
     * @param   string|object  $target  interface or class to create a new instance of
     * @return  \bovigo\callmap\Proxy
     */
    public static function stub($target): Proxy
    {
        return self::callMapClass($target)
                ->newInstanceWithoutConstructor()
                ->preventParentCalls();
    }

    /**
     * returns the class name of any new instance for given target class or interface
     *
     * @api
     * @param   string|object  $target
     * @return  string
     * @since   0.2.0
     */
    public static function classname($target): string
    {
        return self::callMapClass($target)->getName();
    }

    /**
     * returns the proxy class for given target class or interface
     *
     * @param   string|object  $target
     * @return  \ReflectionClass
     */
    private static function callMapClass($target): \ReflectionClass
    {
        $class = self::reflect($target);
        if (!isset(self::$classes[$class->getName()])) {
            self::$classes[$class->getName()] = self::forkCallMapClass($class);
        }

        return self::$classes[$class->getName()];
    }

    /**
     * reflects given class value
     *
     * @param   string|object  $class
     * @return  \ReflectionClass
     * @throws  \InvalidArgumentException
     */
    private static function reflect($class): \ReflectionClass
    {
        if (is_string($class) && (class_exists($class) || interface_exists($class) || trait_exists($class))) {
            return new \ReflectionClass($class);
        } elseif (is_object($class)) {
            return new \ReflectionObject($class);
        }

        throw new \InvalidArgumentException(
                'Given class must either be an existing class, interface or'
                . ' trait name or class instance, ' . \gettype($class)
                . ' with value "' . $class . '" given'
        );
    }

    /**
     * creates a new class from the given class which uses the CallMap trait
     *
     * @param   \ReflectionClass  $class
     * @return  \ReflectionClass
     * @throws  ProxyCreationFailure
     */
    private static function forkCallMapClass(\ReflectionClass $class): \ReflectionClass
    {
        if ($class->isTrait()) {
            $class = self::forkTrait($class);
        }

        try {
            compile(self::createCallmapProxyCode($class));
        } catch (\ParseError $pe) {
            throw new ProxyCreationFailure(
                    'Failure while creating CallMap instance of '
                    . $class->getName() . ': ' . $pe->getMessage(),
                    $pe
            );
        }

        return new \ReflectionClass($class->getName() . 'CallMapProxy');
    }

    /**
     * create an intermediate class for the trait so that any methods of the
     * trait become callable as parent
     *
     * @param   \ReflectionClass  $class
     * @return  \ReflectionClass
     * @throws  ProxyCreationFailure
     */
    private static function forkTrait(\ReflectionClass $class): \ReflectionClass
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
            compile($code);
        } catch (\ParseError $pe) {
            throw new ProxyCreationFailure(
                    'Failure while creating forked trait instance of '
                    . $class->getName() . ': ' . $pe->getMessage(),
                    $pe
            );
        }

        return new \ReflectionClass($class->getName() . 'CallMapFork');
    }

    /**
     * creates code for new class
     *
     * @param   \ReflectionClass  $class
     * @return  string
     */
    private static function createCallmapProxyCode(\ReflectionClass $class): string
    {
        if ($class->isFinal()) {
            throw new \InvalidArgumentException(
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
     * @param   \ReflectionClass $class
     * @return  string
     */
    private static function createClassDefinition(\ReflectionClass $class): string
    {
        return sprintf(
                "class %sCallMapProxy %s \\%s %s\bovigo\callmap\Proxy {\n"
                . "    use \bovigo\callmap\CallMapProxy;\n",
                $class->getShortName(),
                $class->isInterface() ? 'implements ' : 'extends ',
                $class->getName(),
                $class->isInterface() ? ',' : ' implements '
        );
    }

    /**
     * creates methods for the proxy
     *
     * @param  \ReflectionClass  $class
     * @return  string
     */
    private static function createMethods(\ReflectionClass $class): string
    {
        $code    = '';
        $methods = [];
        $params  = [];
        foreach (self::methodsOf($class) as $method) {
            $param = self::params($method);
            /* @var $method \ReflectionMethod */
            $code .= sprintf(
                    "    %s function %s(%s)%s {\n"
                  . "        return \$this->handleMethodCall('%s', func_get_args(), %s);\n"
                  . "    }\n",
                    ($method->isPublic() ? 'public' : 'protected'),
                    $method->getName(),
                    $param['string'],
                    self::determineReturnType($method),
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
        );
    }

    /**
     * determines return type
     *
     * @param   \ReflectionMethod  $method
     * @return  string
     */
    private static function determineReturnType(\ReflectionMethod $method): string
    {
        if (!$method->hasReturnType()) {
            return '';
        }

        $returnType = $method->getReturnType();
        if ($returnType->isBuiltin()) {
            return ': ' . $returnType;
        }

        return ': \\' . $returnType;
    }

    /**
     * returns applicable methods for given class
     *
     * @param   \ReflectionClass  $class
     * @return  \ReflectionMethod[]
     */
    private static function methodsOf(\ReflectionClass $class): \Traversable
    {
        return new \CallbackFilterIterator(
                new \ArrayIterator($class->getMethods()),
                function(\ReflectionMethod $method)
                {
                    return !$method->isPrivate()
                            && !$method->isFinal()
                            && !$method->isStatic()
                            && !$method->isConstructor()
                            && !$method->isDestructor();
                }
        );
    }

    /**
     * returns correct representation of parameters for given method
     *
     * @param   \ReflectionMethod  $method
     * @return  array
     */
    private static function params(\ReflectionMethod $method): array
    {
        $params = [];
        foreach ($method->getParameters() as $parameter) {
            /* @var $parameter \ReflectionParameter */
            $param = '';
            if ($parameter->isArray()) {
                $param .= 'array ';
            } elseif ($parameter->getClass() !== null) {
                $param .= '\\' . $parameter->getClass()->getName() . ' ';
            } elseif ($parameter->isCallable()) {
                $param .= 'callable ';
            } elseif ($parameter->hasType()) {
                $param .= $parameter->getType() . ' ';
            }

            if ($parameter->isPassedByReference()) {
                $param .= '&';
            }

            if ($parameter->isVariadic()) {
                $param .= '...';
            }

            $param .= '$' . $parameter->getName();
            if (!$parameter->isVariadic() && $parameter->isOptional()) {
                if ($method->isInternal() || $parameter->allowsNull()) {
                    $param .= ' = null';
                } elseif (!$parameter->isArray()) {
                    $param .= ' = ' . $parameter->getDefaultValue();
                } else {
                    $param .= ' = ' . var_export($parameter->getDefaultValue(), true);
                }
            }

            $params[$parameter->getName()] = $param;
        }

        return ['names' => array_keys($params), 'string' => join(', ', $params)];
    }

    /**
     * detects whether a method should return the instance or null
     *
     * @param   \ReflectionClass $class
     * @param   \ReflectionMethod $method
     * @return  bool
     */
    private static function shouldReturnSelf(\ReflectionClass $class, \ReflectionMethod $method): bool
    {
        $returnType = self::detectReturnType($method);
        if (in_array($returnType, ['$this', 'self', $class->getName(), $class->getShortName()])) {
            return true;
        }

        foreach ($class->getInterfaces() as $interface) {
            if ($interface->getName() !== 'Traversable' && ($interface->getName() === $returnType || $interface->getShortName() === $returnType)) {
                return true;
            }
        }

        while ($parent = $class->getParentClass()) {
            if ($parent->getName() === $returnType || $parent->getShortName() === $returnType) {
                return true;
            }

            $class = $parent;
        }

        return false;
    }

    /**
     * detects return type of method
     *
     * On PHP 7 it will make use of reflection to detect the return type. In
     * case this does not yield a result or that we run on PHP 5.6 the doc
     * comment will be parsed for the return annotation.
     *
     * @param   \ReflectionMethod  $method
     * @return  string
     */
    private static function detectReturnType(\ReflectionMethod $method)
    {
        if ($method->hasReturnType()) {
            return (string) $method->getReturnType();
        }

        $docComment = $method->getDocComment();
        if (false === $docComment) {
            return null;
        }

        $returnPart = strstr($docComment, '@return');
        if (false === $returnPart) {
            return null;
        }

        $returnParts = explode(' ', trim(str_replace('@return', '', $returnPart)));
        $returnType  = ltrim(trim($returnParts[0]), '\\');
        if (empty($returnType) || strpos($returnType, '*') !== false) {
            return null;
        }

        return $returnType;
    }
}
