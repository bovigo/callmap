<?php
declare(strict_types=1);

namespace bovigo\callmap\internal;

use bovigo\callmap\internal\returntypes\CodedReturnType;
use bovigo\callmap\internal\returntypes\IteratorAggregateReturnTypes;
use bovigo\callmap\internal\returntypes\NoReturn;
use bovigo\callmap\internal\returntypes\StaticReturnType;
use bovigo\callmap\internal\returntypes\UndefinedReturnType;
use IteratorAggregate;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

abstract class ReturnType extends TypeResolver
{
    /**
     * @template T of object
     * @param  ReflectionFunctionAbstract $function
     * @param  ReflectionClass<T>|null    $containingClass
     */
    public static function of(
        ReflectionFunctionAbstract $function,
        ?ReflectionClass $containingClass = null
    ): self {
        $returnType = $function->getReturnType();
        if (null === $returnType) {
            if (
                null !== $containingClass
                && $containingClass->getName() === IteratorAggregate::class
            ) {
                return new IteratorAggregateReturnTypes($function);
            }

            $docComment = $function->getDocComment();
            if (false === $docComment) {
                $docComment = '';
            }

            if (!$function->isInternal()) {
                $class = $containingClass ? $containingClass->getName() . '::' : '';
                trigger_error(
                    'Created proxy for ' . $class . $function->getName() . '() with bovigo/callmap.'
                    . ' Support for methods and functions without a return type declaration is deprecated'
                    . ' and will be removed with 9.0.0.',
                    E_USER_DEPRECATED
                );
            }

            return new UndefinedReturnType($docComment, $containingClass);
        }

        if (
            $returnType instanceof ReflectionUnionType
            || $returnType instanceof ReflectionIntersectionType
        ) {
            return CodedReturnType::forCombined(
                self::resolveCombinedTypes($returnType, $containingClass)
            );
        }

        return self::createFrom($returnType, $function, $containingClass);
    }

    private static function createFrom(
        ReflectionNamedType $returnType,
        ReflectionFunctionAbstract $function,
        ?ReflectionClass $containingClass = null
    ): self {
        if ($returnType->isBuiltin() || StaticReturnType::KEYWORD === $returnType->getName()) {
            return self::createFromBuiltIn($returnType);
        }

        if ($function instanceof ReflectionMethod && 'self' === $returnType->getName()) {
            return CodedReturnType::forClass(
                $returnType,
                $function->getDeclaringClass(),
            );
        }

        return CodedReturnType::from($returnType, $containingClass);
    }

    private static function createFromBuiltIn(ReflectionNamedType $returnType): self
    {
        $self = null;
        switch ($returnType->getName()) {
            case NoReturn::NEVER:
                $self = NoReturn::withNever();
                break;
            case NoReturn::VOID:
                $self = NoReturn::withVoid();
                break;
            case StaticReturnType::KEYWORD:
                $self = new StaticReturnType();
                break;
            default:
                $self = CodedReturnType::from($returnType);
        }

        return $self;
    }

    abstract public function allowsSelfReturn(): bool;

    abstract public function returns(): bool;

    abstract public function code(): string;
}
