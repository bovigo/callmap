<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap;
use function bovigo\assert\assert;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for bovigo\callmap\callable()
 *
 * @since  0.6.0
 */
class WrapCallableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function wrappedCallableIsReturned()
    {
        $callable = function() {};
        $proxy    = NewInstance::of(\ReflectionObject::class, [$this])
                ->mapCalls(['getName' => wrap($callable)]);
        assert($proxy->getName(), isSameAs($callable));
    }

    /**
     * @test
     */
    public function wrappedCallableIsReturnedFromInvocationResults()
    {
        $callable = function() {};
        $proxy    = NewInstance::of(\ReflectionObject::class, [$this])
                ->mapCalls(['getName' => onConsecutiveCalls(wrap($callable))]);
        assert($proxy->getName(), isSameAs($callable));
    }
}

