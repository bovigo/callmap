<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
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
     * @api
     * @param   string|\ReflectionClass  $target           interface or class to create a new instance of
     * @param   mixed[]                  $constructorArgs  optional  list of arguments for the constructor
     * @return  \bovigo\callmap\Proxy
     * @throws  \InvalidArgumentException
     */
    public static function of($target, array $constructorArgs = [])
    {
        $class = self::reflect($target);
        if (!isset(self::$classes[$class->getName()])) {
            self::$classes[$class->getName()] = self::forkCallMapClass($class);
        }

        return self::$classes[$class->getName()]
                ->newInstanceArgs($constructorArgs);
    }

    /**
     * reflects given class value
     *
     * @param   string|object  $class
     * @return  \ReflectionClass
     * @throws  \InvalidArgumentException
     */
    private static function reflect($class)
    {
        if (is_string($class) && (class_exists($class) || interface_exists($class))) {
            return new \ReflectionClass($class);
        } elseif (is_object($class)) {
            return new \ReflectionObject($class);
        }

        throw new \InvalidArgumentException(
                'Given class must either be an existing class or interface name'
                . ' or class instance, ' . \gettype($class)
                . ' with value "' . $class . '" given'
        );
    }

    /**
     * creates a new class from the given class which uses the CallMap trait
     *
     * @param   \ReflectionClass  $class
     * @return  \ReflectionClass
     * @throws  \ReflectionException
     */
    private static function forkCallMapClass(\ReflectionClass $class)
    {
        if (false === eval(self::createCallmapClassCode($class))) {
            throw new \ReflectionException(
                    'Failure while creating CallMap instance of '
                    . $class->getName()
            );
        }

        return new \ReflectionClass($class->getName() . 'CallMapProxy');
    }

    /**
     * creates code for new class
     *
     * @param   \ReflectionClass  $class
     * @return  string
     */
    private static function createCallmapClassCode(\ReflectionClass $class)
    {
        $code = sprintf(
                "class %sCallMapProxy %s \\%s %s\bovigo\callmap\Proxy{\n    use \bovigo\callmap\CallMap;\n",
                $class->getShortName(),
                $class->isInterface() ? 'implements' : 'extends',
                $class->getName(),
                $class->isInterface() ? ',' : ' implements'
        );
        foreach (self::methodsOf($class) as $method) {
            /* @var  $method \ReflectionMethod */
            $code .= sprintf(
                    "    %s function %s(%s) {\n"
                  . "        return \$this->handleMethodCall('%s', func_get_args());\n"
                  . "    }\n",
                    ($method->isPublic() ? 'public' : 'protected'),
                    $method->getName(),
                    self::params($method),
                    $method->getName()
            );
        }

        $code .= "}\n";
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
     * returns applicable methods for given class
     *
     * @param   \ReflectionClass  $class
     * @return  \ReflectionMethod[]
     */
    private static function methodsOf(\ReflectionClass $class)
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
     * @return  string
     */
    private static function params(\ReflectionMethod $method)
    {
        $params = '';
        foreach ($method->getParameters() as $parameter) {
            /* @var $parameter \ReflectionParameter */
            if (strlen($params) > 0) {
                $params .= ', ';
            }

            if ($parameter->isArray()) {
                $params .= 'array ';
            } elseif ($parameter->getClass() !== null) {
                $params .= '\\' . $parameter->getClass()->getName() . ' ';
            } elseif ($parameter->isCallable()) {
                $params .= 'callable ';
            }

            $params .= '$' . $parameter->getName();
            if ($parameter->allowsNull() || $method->isInternal()) {
                $params .= ' = null';
            } elseif ($parameter->isOptional()) {
                $params .= ' = ' . ($parameter->isArray() ? '[]' : $parameter->getDefaultValue());
            }
        }

        return $params;
    }
}
