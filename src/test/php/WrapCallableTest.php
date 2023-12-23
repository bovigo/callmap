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
namespace bovigo\callmap;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for bovigo\callmap\callable()
 *
 * @since 0.6.0
 */
class WrapCallableTest extends TestCase
{
    #[Test]
    public function wrappedCallableIsReturned(): void
    {
        $callable = function() {};
        $proxy    = NewInstance::of(ReflectionObject::class, [$this])
            ->returns(['getName' => wrap($callable)]);
        assertThat($proxy->getName(), isSameAs($callable));
    }

    #[Test]
    public function wrappedCallableIsReturnedFromInvocationResults(): void
    {
        $callable = function() {};
        $proxy    = NewInstance::of(ReflectionObject::class, [$this])
            ->returns(['getName' => onConsecutiveCalls(wrap($callable))]);
        assertThat($proxy->getName(), isSameAs($callable));
    }
}
