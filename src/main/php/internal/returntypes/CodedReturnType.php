<?php
declare(strict_types=1);

namespace bovigo\callmap\internal\returntypes;

use bovigo\callmap\internal\ReturnType;
use ReflectionClass;
use ReflectionNamedType;
use Traversable;

class CodedReturnType extends ReturnType
{
    protected function __construct(
        protected string $returnType,
        private bool $allowsNull,
        private bool $isClass = true
    ) { }

    public static function from(ReflectionNamedType $namedType): self
    {
        return new self(
            $namedType->getName(),
            $namedType->allowsNull() && $namedType->getName() !== 'mixed',
            !$namedType->isBuiltin()
        );
    }

    public static function forClass(
        ReflectionNamedType $namedType,
        string $type
    ): self {
        return new self(
            $type,
            $namedType->allowsNull()
        );
    }

    public static function forCombined(string $types): self
    {
        return new self(
            $types,
            false,
            false
        );
    }

    public function allowsSelfReturn(ReflectionClass $class): bool
    {
        if (!$this->isClass || $this->allowsNull) {
            return false;
        }

        if (
            $this->isOfType($class)
            || $this->implementsInterfaceOf($class)
            || $this->isParentOf($class)
        ) {
            return true;
        }

        return false;
    }

    protected function isOfType(ReflectionClass $class): bool
    {
        return $class->getName() === $this->returnType
            || $class->getShortName() === $this->returnType;
    }

    protected function implementsInterfaceOf(ReflectionClass $class): bool
    {
        foreach ($class->getInterfaces() as $interface) {
            if ($interface->getName() !== Traversable::class && $this->isOfType($interface)) {
                return true;
            }
        }

        return false;
    }

    protected function isParentOf(ReflectionClass $class): bool
    {
        while ($parent = $class->getParentClass()) {
            if ($this->isOfType($parent)) {
                return true;
            }

            $class = $parent;
        }

        return false;
    }

    public function returns(): bool
    {
        return true;
    }

    public function code(): string
    {
        return sprintf(
            ': %s%s%s',
            $this->allowsNull ? '?' : '',
            $this->isClass ? '\\' : '',
            $this->returnType
        );
    }
}
