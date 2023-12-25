<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap\internal;

use bovigo\callmap\internal\returntypes\IteratorAggregateReturnTypes;
use IteratorAggregate;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;

/**
 * @since 8.0.1
 */
class IteratorAggregateTest extends TestCase
{
    #[Test]
    public function foo(): void
    {
        $method = 'getIterator';
        $refClass = new ReflectionClass(IteratorAggregate::class);
        $returnType = ReturnType::of(
            $refClass->getMethod($method),
            $refClass
        );

        assertThat(
            $returnType->code(),
            equals(IteratorAggregateReturnTypes::METHODS[$method])
        );
    }
}