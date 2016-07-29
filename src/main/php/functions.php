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
     * creates an invocation result which throws the given exception
     *
     * @api
     * @param   \Throwable  $e
     * @return  \bovigo\callmap\InvocationThrow
     * @since   0.2.0
     */
    function throws(\Throwable $e): InvocationThrow
    {
        return new InvocationThrow($e);
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
     * @return  \bovigo\callmap\Verification
     * @since   0.5.0
     */
    function verify(Proxy $proxy, string $method = ''): Verification
    {
        return new Verification($proxy->invocations($method));
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
        if (!$function->hasReturnType()) {
            return '';
        }

        $returnType = $function->getReturnType();
        if ($returnType->isBuiltin()) {
            return ': ' . $returnType;
        }

        if ('self' == $returnType) {
            return ': \\' . $function->getDeclaringClass()->getName();
        }

        return ': \\' . $returnType;
    }

    /**
     * returns correct representation of parameters for given method
     *
     * @internal
     * @param   \ReflectionFunctionAbstract  $function
     * @return  array
     */
    function paramsOf(\ReflectionFunctionAbstract $function): array
    {
        $params = [];
        foreach ($function->getParameters() as $parameter) {
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
     * blacklist our own classes from being displayed in PHPUnit error stacks
     */
    if (class_exists('\PHPUnit_Util_Blacklist')) {
        \PHPUnit_Util_Blacklist::$blacklistedClassNames = array_merge(
                \PHPUnit_Util_Blacklist::$blacklistedClassNames,
                [Verification::class => 1]
        );
    }
}
