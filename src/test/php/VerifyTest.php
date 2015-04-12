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
/**
 * Helper for the test.
 */
class Verified
{
    public function aMethod($roland = 303)
    {

    }
}
/**
 * Test for bovigo\callmap\verify()
 *
 * @since  0.5.0
 */
class VerifyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  \bovigo\callmap\Proxy
     */
    private $proxy;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->proxy = NewInstance::of('bovigo\callmap\Verified');
    }

    /**
     * @test
     */
    public function wasNeverCalledReturnsTrueWhenNeverCalled()
    {
        assertTrue(verify($this->proxy, 'aMethod')->wasNeverCalled());
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\CallAmountViolation
     * @expectedExceptionMessage bovigo\callmap\Verified::aMethod() was not expected to be called, but actually called 1 time(s)
     */
    public function wasNeverCalledThrowsCallAmountViolationWhenMethodWasCalled()
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function wasCalledReturnsTrueWhenCalledExactlyWithGivenAmount()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        assertTrue(verify($this->proxy, 'aMethod')->wasCalled(2));
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\CallAmountViolation
     * @expectedExceptionMessage bovigo\callmap\Verified::aMethod() was expected to be called 2 time(s), but actually called 1 time(s).
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooSeldom()
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalled(2);
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\CallAmountViolation
     * @expectedExceptionMessage bovigo\callmap\Verified::aMethod() was expected to be called 2 time(s), but actually called 3 time(s).
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooOften()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalled(2);
    }

    /**
     * @test
     */
    public function wasCalledOnceReturnsTrueWhenCalledExactlyOnce()
    {
        $this->proxy->aMethod();
        assertTrue(verify($this->proxy, 'aMethod')->wasCalledOnce());
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\CallAmountViolation
     * @expectedExceptionMessage bovigo\callmap\Verified::aMethod() was expected to be called once, but actually never called.
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledLessThanOnce()
    {
        verify($this->proxy, 'aMethod')->wasCalledOnce();
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\CallAmountViolation
     * @expectedExceptionMessage bovigo\callmap\Verified::aMethod() was expected to be called once, but actually called 2 time(s).
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledMoreThanOnce()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function wasCalledAtLeastReturnsTrueWhenCalledExactlyMinimumAmount()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        assertTrue(verify($this->proxy, 'aMethod')->wasCalledAtLeast(2));
    }

    /**
     * @test
     */
    public function wasCalledAtLeastReturnsTrueWhenCalledMoreThanMinimumAmount()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        assertTrue(verify($this->proxy, 'aMethod')->wasCalledAtLeast(2));
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\CallAmountViolation
     * @expectedExceptionMessage bovigo\callmap\Verified::aMethod() was expected to be called at least 2 time(s), but actually called 1 time(s).
     */
    public function wasCalledAtLeastThrowsCallAmountViolationWhenCalledLessThanMinimumAmount()
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeast(2);
    }

    /**
     * @test
     */
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledExactlyOnce()
    {
        $this->proxy->aMethod();
        assertTrue(verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce());
    }

    /**
     * @test
     */
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledMoreThanOnce()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        assertTrue(verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce());
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\CallAmountViolation
     * @expectedExceptionMessage bovigo\callmap\Verified::aMethod() was expected to be called at least once, but actually never called.
     */
    public function wasCalledAtLeastOnceThrowsCallAmountViolationWhenCalledLessThanOnce()
    {
        verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce();
    }

    /**
     * @test
     */
    public function wasCalledAtMostReturnsTrueWhenCalledExactlyMaximumAmount()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        assertTrue(verify($this->proxy, 'aMethod')->wasCalledAtMost(2));
    }

    /**
     * @test
     */
    public function wasCalledAtMostOnceReturnsTrueWhenCalledLessThanMaximumAmount()
    {
        $this->proxy->aMethod();
        assertTrue(verify($this->proxy, 'aMethod')->wasCalledAtMost(2));
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\CallAmountViolation
     * @expectedExceptionMessage bovigo\callmap\Verified::aMethod() was expected to be called at most 2 time(s), but actually called 3 time(s).
     */
    public function wasCalledAtMostOnceThrowsCallAmountViolationWhenCalledMoreThanMaximumAmount()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtMost(2);
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\MissingInvocation
     * @expectedExceptionMessage  Missing invocation #1 for aMethod(), never invoked
     */
    public function verifyArgumentsForMethodNotCalledThrowsMissingInvocation()
    {
        verify($this->proxy, 'aMethod')->receivedNothing();
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\MissingInvocation
     * @expectedExceptionMessage  Missing invocation #2 for aMethod(), only invoked once
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation6()
    {
        $this->proxy->aMethod(808);
        verify($this->proxy, 'aMethod')->receivedOn(2, 808);
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\MissingInvocation
     * @expectedExceptionMessage  Missing invocation #3 for aMethod(), only invoked 2 times
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation()
    {
        $this->proxy->aMethod(808);
        $this->proxy->aMethod(808);
        verify($this->proxy, 'aMethod')->receivedOn(3, 808);
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\ArgumentMismatch
     * @expectedExceptionMessage  Argument count for invocation #1 of bovigo\callmap\Verified::aMethod() is too high: received 1 argument(s), expected no arguments.
     */
    public function verifyReceivedNothingThrowsArgumentMismatchWhenArgumentsReceived()
    {
        $this->proxy->aMethod(808);
        verify($this->proxy, 'aMethod')->receivedNothing();
    }

    /**
     * @test
     * @expectedException  bovigo\callmap\ArgumentMismatch
     * @expectedExceptionMessage  Argument count for invocation #1 of bovigo\callmap\Verified::aMethod() is too low: received 0 argument(s), expected 1 argument(s).
     */
    public function verifyReceivedThrowsArgumentMismatchWhenLessArgumentsReceivedThanExpected()
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->received(808);
    }
}
