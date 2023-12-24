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
        private ?ReflectionClass $declaringClass = null
    ) { }

    public static function from(
        ReflectionNamedType $namedType,
        ?ReflectionClass $declaringClass = null
    ): self {
        return new self(
            $namedType->getName(),
            $namedType->allowsNull() && $namedType->getName() !== 'mixed',
            $declaringClass
        );
    }

    public static function forClass(
        ReflectionNamedType $namedType,
        ReflectionClass $declaringClass
    ): self {
        return new self(
            $declaringClass->getName(),
            $namedType->allowsNull(),
            $declaringClass
        );
    }

    public static function forCombined(string $types): self
    {
        return new self(
            $types,
            false
        );
    }

    public function allowsSelfReturn(): bool
    {
        if (null === $this->declaringClass || $this->allowsNull) {
            return false;
        }

        if (
            $this->isOfType($this->declaringClass)
            || $this->implementsInterfaceFrom($this->declaringClass)
            || $this->isParentOf($this->declaringClass)
        ) {
            return true;
        }

        return false;
    }

    private function isOfType(ReflectionClass $class): bool
    {
        return $class->getName() === $this->returnType
            || $class->getShortName() === $this->returnType;
    }

    private function implementsInterfaceFrom(ReflectionClass $class): bool
    {
        foreach ($class->getInterfaces() as $interface) {
            if ($interface->getName() !== Traversable::class && $this->isOfType($interface)) {
                return true;
            }
        }

        return false;
    }

    private function isParentOf(ReflectionClass $class): bool
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
            (class_exists($this->returnType) || interface_exists($this->returnType)) ? '\\' : '',
            $this->returnType
        );
    }
}
