<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap {
    use bovigo\callmap\verification\Verification;
    use Closure;
    use ReflectionClass;
    use ReflectionFunctionAbstract;
    use ReflectionIntersectionType;
    use ReflectionMethod;
    use ReflectionNamedType;
    use ReflectionUnionType;
    use Throwable;
    use UnexpectedValueException;

    /**
     * creates a closure which throws the given exception when invoked
     *
     * @api
     * @since 0.2.0
     */
    function throws(Throwable $e): Closure
    {
        return function() use ($e) { throw $e; };
    }

    /**
     * wraps given callable into a closure so that the given callable is returned and not executed
     *
     * @api
     * @since 0.6.0
     */
    function wrap(callable $callable): Closure
    {
        return function() use ($callable) { return $callable; };
    }

    /**
     * creates a list of invocation results
     *
     * @api
     * @since 0.2.0
     */
    function onConsecutiveCalls(mixed ...$values): InvocationResults
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
     * @since 0.5.0
     */
    function verify(Proxy $proxy, string $method = ''): Verification
    {
        return new Verification($proxy->invocations($method));
    }

    /**
     * determines return type
     *
     * @internal
     * @template T of object
     * @param  ReflectionFunctionAbstract $function
     * @param  ReflectionClass<T>|null    $containingClass
     * @return string
     * @throws UnexpectedValueException
     */
    function determineReturnTypeOf(
        ReflectionFunctionAbstract $function,
        ?ReflectionClass $containingClass = null
    ): string {
        $returnType = $function->getReturnType();
        if (null === $returnType) {
            return '';
        }

        if (
            $returnType instanceof ReflectionUnionType
            || $returnType instanceof ReflectionIntersectionType
        ) {
            return ': ' . resolveCombinedTypes($returnType, $containingClass);
        }

        /** @var \ReflectionNamedType $returnType */
        if ($returnType->isBuiltin()) {
            return ': '
                . ($returnType->allowsNull() && $returnType->getName() !== 'mixed' ? '?' : '')
                . $returnType->getName();
        }

        if ('self' == $returnType->getName()) {
            if ($function instanceof ReflectionMethod) {
                return ': ' . ($returnType->allowsNull() ? '?' : '') . '\\' . $function->getDeclaringClass()->getName();
            }

            throw new UnexpectedValueException(
                'Function ' . $function->getName() . ' defines return type self but that is not possible.'
            );
        } elseif ('static' === $returnType->getName()) {
            return ': static';
        }

        return ': ' . ($returnType->allowsNull() ? '?' : '') . '\\' . $returnType->getName();
    }

    /**
     * returns correct representation of parameters for given method
     *
     * @internal
     * @template T of object
     * @param ReflectionFunctionAbstract $function
     * @param ReflectionClass<T>|null    $containingClass
     * @return array<string,mixed>
     */
    function paramsOf(
        ReflectionFunctionAbstract $function,
        ?ReflectionClass $containingClass = null
    ): array {
        $params = [];
        foreach ($function->getParameters() as $parameter) {
            /** @var \ReflectionParameter $parameter */
            $param = '';
            $paramType = $parameter->getType();
            if (
                null !== $paramType
                && ($paramType instanceof ReflectionUnionType
                || $paramType instanceof ReflectionIntersectionType)
            ) {
                $param .= resolveCombinedTypes($paramType, $containingClass) . ' ';
            } elseif ($paramType instanceof ReflectionNamedType) {
                $param .= resolveType($paramType->getName(), $containingClass);
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
     * Resolves union and intersection types so that any generaded code is compatible signature wise.
     *
     * @internal
     * @since   6.2.0
     * @template T of object
     * @param ReflectionUnionType|ReflectionIntersectionType $unionType
     * @param ReflectionClass<T>|null $containingClass
     * @return string
     */
    function resolveCombinedTypes(
        ReflectionUnionType|ReflectionIntersectionType $unionType,
        ?ReflectionClass $containingClass = null
    ): string {
        $types = [];
        foreach ($unionType->getTypes() as $type) {
            if ($type instanceof ReflectionNamedType) {
                $type = $type->getName();
                $types[] = resolveType($type, $containingClass);
            } else {
                $types[] = '(' . resolveCombinedTypes($type, $containingClass) . ')';
            }
        }

        return join(
            $unionType instanceof ReflectionUnionType ? '|' : '&',
            $types
        );
    }

    /**
     * Converts type string to proper formatted type.
     *
     * @internal
     * @since   6.2.0
     * @template T of object
     * @param string $type
     * @param ReflectionClass<T>|null $containingClass
     * @return string
     */
    function resolveType(string $type, ?ReflectionClass $containingClass): string
    {
        if ('self' === $type && null !== $containingClass) {
            return '\\' . $containingClass->getName();
        } elseif (class_exists($type) || interface_exists($type)) {
            return '\\' . $type;
        }

        return $type;
    }

    /**
     * internal helper function to be able to mock eval in tests
     *
     * Since eval() is a language construct and not a function but we want to
     * mock it when testing dynamic code creation we wrap it into our own
     * function.
     *
     * @since 3.0.0
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
