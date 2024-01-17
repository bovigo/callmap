<?php
declare(strict_types=1);

namespace bovigo\callmap\internal;

use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

/**
 * @internal
 */
class Parameters extends TypeResolver
{
    /** @var string[] */
    private array $names = [];
    private string $code;

    /**
     * @template T of object
     * @param ReflectionFunctionAbstract $function
     * @param ReflectionClass<T>|null    $containingClass
     */
    private function __construct(
        ReflectionFunctionAbstract $function,
        private ?ReflectionClass $containingClass = null
    ) {
        $paramsCode = [];
        foreach ($function->getParameters() as $parameter) {
            $this->names[] = $parameter->getName();
            $paramsCode[] = $this->createCodeFor($parameter);
        }

        $this->code = join(', ', $paramsCode);
    }

    /**
     * @template T of object
     * @param ReflectionFunctionAbstract $function
     * @param ReflectionClass<T>|null    $containingClass
     */
    public static function of(
        ReflectionFunctionAbstract $function,
        ?ReflectionClass $containingClass = null
    ): self {
        return new self($function, $containingClass);
    }

    /**
     * @return string[]
     */
    public function names(): array
    {
        return $this->names;
    }

    public function code(): string
    {
        return $this->code;
    }

    private function createCodeFor(ReflectionParameter $parameter): string
    {
        return sprintf(
            '%s%s %s%s$%s%s',
            $this->isNonOptionalNullable($parameter) ? '?' : '',
            $this->resolve($parameter->getType()),
            $parameter->isPassedByReference() ? '&' : '',
            $parameter->isVariadic() ? '...' : '',
            $parameter->getName(),
            $this->defaultValue($parameter)
        );
    }

    private function isNonOptionalNullable(ReflectionParameter $parameter): bool
    {
        $type = $parameter->getType();
        return $type instanceof ReflectionNamedType
            && 'mixed' !== $type->getName()
            && ($parameter->isOptional() || $parameter->allowsNull());
    }

    private function defaultValue(ReflectionParameter $parameter): string
    {
        if (!$parameter->isVariadic() && $parameter->isOptional()) {
            if ($parameter->isDefaultValueAvailable()) {
                return ' = ' . var_export($parameter->getDefaultValue(), true);
            }

            return ' = null';
        }

        return '';
    }

    private function resolve(
        ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $paramType
    ): string {
        if (
            $paramType instanceof ReflectionUnionType
            || $paramType instanceof ReflectionIntersectionType
        ) {
            return self::resolveCombinedTypes($paramType, $this->containingClass);
        }
        
        if ($paramType instanceof ReflectionNamedType) {
            return self::resolveType($paramType->getName(), $this->containingClass);
        }

        return '';
    }
}
