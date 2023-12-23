<?php
declare(strict_types=1);

namespace bovigo\callmap\internal;

use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

use function bovigo\callmap\resolveCombinedTypes;
use function bovigo\callmap\resolveType;

/**
 * @internal
 */
class Parameters
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
        private ReflectionFunctionAbstract $function,
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
        $paramCode = $this->resolve($parameter->getType());
        if ($parameter->isPassedByReference()) {
            $paramCode .= '&';
        }

        if ($parameter->isVariadic()) {
            $paramCode .= '...';
        }

        $paramCode .= '$' . $parameter->getName();
        if (!$parameter->isVariadic() && $parameter->isOptional()) {
            if ($this->function->isInternal() || $parameter->allowsNull()) {
                $paramCode .= ' = null';
            } else {
                $paramCode .= ' = ' . var_export($parameter->getDefaultValue(), true);
            }
        }

        return $paramCode;
    }

    private function resolve(
        ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $paramType
    ): string {
        if (
            $paramType instanceof ReflectionUnionType
            || $paramType instanceof ReflectionIntersectionType
        ) {
            return resolveCombinedTypes($paramType, $this->containingClass) . ' ';
        }
        
        if ($paramType instanceof ReflectionNamedType) {
            return resolveType($paramType->getName(), $this->containingClass);
        }

        return '';
    }
}
