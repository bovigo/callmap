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
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for bovigo\callmap\callable()
 *
 * @since  0.6.0
 */
class WrapCallableTest extends TestCase
{
    /**
     * @test
     */
    public function wrappedCallableIsReturned()
    {
        $callable = function() {};
        $proxy    = NewInstance::of(\ReflectionObject::class, [$this])
                ->returns(['getName' => wrap($callable)]);
        assertThat($proxy->getName(), isSameAs($callable));
    }

    /**
     * @test
     */
    public function wrappedCallableIsReturnedFromInvocationResults()
    {
        $callable = function() {};
        $proxy    = NewInstance::of(\ReflectionObject::class, [$this])
                ->returns(['getName' => onConsecutiveCalls(wrap($callable))]);
        assertThat($proxy->getName(), isSameAs($callable));
    }
}
