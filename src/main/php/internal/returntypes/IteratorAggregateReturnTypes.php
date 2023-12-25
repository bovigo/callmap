<?php
declare(strict_types=1);

namespace bovigo\callmap\internal\returntypes;

use bovigo\callmap\internal\ReturnType;
use ReflectionMethod;

/**
 * Standin to provide correct return type for \IteratorAggregate
 * as reflecting \IteratorAggregate::getIterator() doesn't provide
 * a return type.
 *
 * @since 8.0.1
 */
class IteratorAggregateReturnTypes extends ReturnType
{
    public const METHODS = ['getIterator' => ': \Traversable'];

    public function __construct(private ReflectionMethod $method) { }

    public function allowsSelfReturn(): bool
    {
        return false;
    }

    public function returns(): bool
    {
        return true;
    }

    public function code(): string
    {
        return self::METHODS[$this->method->getName()] ?? '';
    }
}