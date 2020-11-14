<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap {
    /**
     * creates a closure which throws the given exception when invoked
     *
     * @api
     * @param   \Throwable  $e
     * @return  \Closure
     * @since   0.2.0
     */
    function throws(\Throwable $e): \Closure
    {
        return function() use ($e) { throw $e; };
    }

    /**
     * wraps given callable into a closure so that the given callable is returned and not executed
     *
     * @api
     * @param   callable  $callable
     * @return  \Closure
     * @since   0.6.0
     */
    function wrap(callable $callable): \Closure
    {
        return function() use ($callable) { return $callable; };
    }

    /**
     * creates a list of invocation results
     *
     * @api
     * @param   mixed  ...$values
     * @return  \bovigo\callmap\InvocationResults
     * @since   0.2.0
     */
    function onConsecutiveCalls(...$values): InvocationResults
    {
        return new InvocationResults($values);
    }

    /**
     * returns possibilities to verify method or function invocations
     *
     * Parameter $method can be left away when the proxy to be verified is a
     * function proxy. For a class proxy the parameter is required.
     *
     * @api
     * @param   \bovigo\callmap\Proxy  $proxy   callmap to verify
     * @param   string                 $method  optional  actual method to verify
     * @return  \bovigo\callmap\verification\Verification
     * @since   0.5.0
     */
    function verify(Proxy $proxy, string $method = ''): \bovigo\callmap\verification\Verification
    {
        return new \bovigo\callmap\verification\Verification($proxy->invocations($method));
    }

    /**
     * determines return type
     *
     * @internal
     * @param   \ReflectionFunctionAbstract  $function
     * @return  string
     */
    function determineReturnTypeOf(\ReflectionFunctionAbstract $function): string
    {
        $returnType = $function->getReturnType();
        if (null === $returnType) {
            return '';
        }

        if ($returnType instanceof \ReflectionUnionType) {
            return ': ' . (string) $returnType;
        }

        /** @var \ReflectionNamedType $returnType */
        if ($returnType->isBuiltin()) {
            return ': ' . ($returnType->allowsNull() ? '?' : '') . $returnType->getName();
        }

        if ('self' == $returnType->getName()) {
            if ($function instanceof \ReflectionMethod) {
                return ': ' . ($returnType->allowsNull() ? '?' : '') . '\\' . $function->getDeclaringClass()->getName();
            }

            throw new \UnexpectedValueException('Function ' . $function->getName() . ' defines return type self but that is not possible.');
        }

        return ': ' . ($returnType->allowsNull() ? '?' : '') . '\\' . $returnType->getName();
    }

    /**
     * returns correct representation of parameters for given method
     *
     * @internal
     * @param   \ReflectionFunctionAbstract  $function
     * @return  array<string,mixed>
     */
    function paramsOf(\ReflectionFunctionAbstract $function): array
    {
        $params = [];
        foreach ($function->getParameters() as $parameter) {
            /* @var $parameter \ReflectionParameter */
            $param = '';
            $paramType = $parameter->getType();
            if (null !== $paramType && $paramType instanceof \ReflectionUnionType) {
                $types = [];
                foreach ($paramType->getTypes() as $type) {
                    $type = $type->getName();
                    if (class_exists($type) || interface_exists($type)) {
                        $types[] =  '\\' . $type;
                    } else {
                        $types[] =  $type;
                    }
                }

                $param .= join('|', $types) . ' ';
            } elseif ($parameter->isArray()) {
                $param .= 'array ';
            } elseif ($parameter->getClass() !== null) {
                $param .= '\\' . $parameter->getClass()->getName() . ' ';
            } elseif ($parameter->isCallable()) {
                $param .= 'callable ';
            } elseif (null !== $paramType && $paramType instanceof \ReflectionNamedType) {
                $param .= $paramType->getName() . ' ';
            }

            if ($parameter->isPassedByReference()) {
                $param .= '&';
            }

            if ($parameter->isVariadic()) {
                $param .= '...';
            }

            $param .= '$' . $parameter->getName();
            if (!$parameter->isVariadic() && $parameter->isOptional()) {
                if ($function->isInternal() || $parameter->allowsNull()) {
                    $param .= ' = null';
                } else {
                    $param .= ' = ' . var_export($parameter->getDefaultValue(), true);
                }
            }

            $params[$parameter->getName()] = $param;
        }

        return ['names' => array_keys($params), 'string' => join(', ', $params)];
    }

    /**
     * internal helper function to be able to mock eval in tests
     *
     * Since eval() is a language construct and not a function but we want to
     * mock it when testing dynamic code creation we wrap it into our own
     * function.
     *
     * @param   string  $code
     * @return  bool
     * @since   3.0.0
     * @internal
     */
    function compile(string $code)
    {
        return eval($code);
    }

    /**
     * exclude some of our own classes from being displayed in PHPUnit error stacks
     */
    if (class_exists(\PHPUnit\Util\ExcludeList::class)) {
        \PHPUnit\Util\ExcludeList::addDirectory(__DIR__ . DIRECTORY_SEPARATOR);
    }
}
