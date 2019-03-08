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

use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Helper for the test.
 */
class Verified
{
    public function aMethod(int $roland = 303)
    {

    }

    public function otherMethod(int $roland = 909)
    {

    }
}
/**
 * Test for bovigo\callmap\verify() with class proxy.
 *
 * @since  0.5.0
 * @group  verify
 */
class VerifyClassProxyTest extends TestCase
{
    /**
     * @type  \bovigo\callmap\ClassProxy
     */
    private $proxy;

    /**
     * set up test environment
     */
    public function setUp(): void
    {
        $this->proxy = NewInstance::of(Verified::class);
    }

    /**
     * @test
     * @since  3.1.0
     */
    public function verifyWithoutMethodNameThrowsInvalidArgumentException()
    {
        expect(function() { verify($this->proxy); })
                ->throws(\InvalidArgumentException::class)
                ->withMessage('Please provide a method name to retrieve invocations for.');
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
     */
    public function wasNeverCalledThrowsCallAmountViolationWhenMethodWasCalled()
    {
        $this->proxy->aMethod();
        expect(function() {
            verify($this->proxy, 'aMethod')->wasNeverCalled();
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        'bovigo\callmap\Verified::aMethod() was not expected to'
                        . ' be called, but actually called 1 time(s).'
                );
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
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooSeldom()
    {
        $this->proxy->aMethod();
        expect(function() {
                verify($this->proxy, 'aMethod')->wasCalled(2);
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        'bovigo\callmap\Verified::aMethod() was expected to be'
                        . ' called 2 time(s), but actually called 1 time(s).'
                );
    }

    /**
     * @test
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooOften()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(function() {
                verify($this->proxy, 'aMethod')->wasCalled(2);
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        'bovigo\callmap\Verified::aMethod() was expected to be'
                        . ' called 2 time(s), but actually called 3 time(s).'
                );
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
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledLessThanOnce()
    {
        expect(function() {
                verify($this->proxy, 'aMethod')->wasCalledOnce();
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        'bovigo\callmap\Verified::aMethod() was expected to be'
                        . ' called once, but actually never called.'
                );
    }

    /**
     * @test
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledMoreThanOnce()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(function() {
                verify($this->proxy, 'aMethod')->wasCalledOnce();
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        'bovigo\callmap\Verified::aMethod() was expected to be'
                        . ' called once, but actually called 2 time(s).'
                );
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
     */
    public function wasCalledAtLeastThrowsCallAmountViolationWhenCalledLessThanMinimumAmount()
    {
        $this->proxy->aMethod();
        expect(function() {
                verify($this->proxy, 'aMethod')->wasCalledAtLeast(2);
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        'bovigo\callmap\Verified::aMethod() was expected to be'
                        . ' called at least 2 time(s), but actually called 1 time(s).'
                );
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
     */
    public function wasCalledAtLeastOnceThrowsCallAmountViolationWhenCalledLessThanOnce()
    {
        expect(function() {
                verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce();
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        'bovigo\callmap\Verified::aMethod() was expected to be'
                        . ' called at least once, but was never called.'
                );
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
     */
    public function wasCalledAtMostOnceThrowsCallAmountViolationWhenCalledMoreThanMaximumAmount()
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(function() {
                verify($this->proxy, 'aMethod')->wasCalledAtMost(2);
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        'bovigo\callmap\Verified::aMethod() was expected to be'
                        . ' called at most 2 time(s), but actually called 3 time(s).'
                );
    }

    /**
     * @test
     */
    public function verifyArgumentsForMethodNotCalledThrowsMissingInvocation()
    {
        expect(function() {
                verify($this->proxy, 'aMethod')->receivedNothing();
        })
                ->throws(MissingInvocation::class)
                ->withMessage(
                        'Missing invocation #1 for bovigo\callmap\Verified::aMethod(),'
                        . ' was never called.'
                );
    }

    /**
     * @test
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation6()
    {
        $this->proxy->aMethod(808);
        expect(function() {
                verify($this->proxy, 'aMethod')->receivedOn(2, 808);
        })
                ->throws(MissingInvocation::class)
                ->withMessage(
                        'Missing invocation #2 for bovigo\callmap\Verified::aMethod(),'
                        . ' was only called once.');
    }

    /**
     * @test
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation()
    {
        $this->proxy->aMethod(808);
        $this->proxy->aMethod(808);
        expect(function() {
                verify($this->proxy, 'aMethod')->receivedOn(3, 808);
        })
                ->throws(MissingInvocation::class)
                ->withMessage(
                        'Missing invocation #3 for bovigo\callmap\Verified::aMethod(),'
                        . ' was only called 2 times.'
                );
    }

    /**
     * @test
     */
    public function verifyReceivedNothingThrowsArgumentMismatchWhenArgumentsReceived()
    {
        $this->proxy->aMethod(808);
        expect(function() {
                verify($this->proxy, 'aMethod')->receivedNothing();
        })
                ->throws(ArgumentMismatch::class)
                ->withMessage(
                        'Argument count for invocation #1 of bovigo\callmap\Verified::aMethod()'
                        . ' is too high: received 1 argument(s), expected no arguments.'
                );
    }

    /**
     * @test
     */
    public function verifyReceivedThrowsArgumentMismatchWhenLessArgumentsReceivedThanExpected()
    {
        $this->proxy->aMethod();
        expect(function() {
                verify($this->proxy, 'aMethod')->received(808);
        })
                ->throws(ArgumentMismatch::class)
                ->withMessage(
                        'Argument count for invocation #1 of bovigo\callmap\Verified::aMethod()'
                        . ' is too low: received 0 argument(s), expected 1 argument(s).'
                );
    }

    /**
     * @test
     * @since  0.6.0
     */
    public function verifyReceivedPassesExceptionThrownByConstraint()
    {
        $this->proxy->aMethod(303);
        expect(function() {
                verify($this->proxy, 'aMethod')->received(808);
        })
                ->throws(\PHPUnit\Framework\AssertionFailedError::class)
                ->withMessage(
                    'Failed asserting that 303 is equal to 808.
Parameter $roland at position 0 for invocation #1 of bovigo\callmap\Verified::aMethod()'
                    . ' does not match expected value.'
                );
    }

    /**
     * @test
     * @since  2.0.0
     */
    public function verifyWithPredicate()
    {
        $this->proxy->aMethod(303);
        verify($this->proxy, 'aMethod')->received(equals(303));
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function verifyWithPhpUnitConstraint()
    {
        $this->proxy->aMethod(303);
        verify($this->proxy, 'aMethod')
                ->received(new \PHPUnit\Framework\Constraint\IsEqual(303));
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function canVerifyArgumentsForNonMappedMethod()
    {
        $this->proxy->returns(['aMethod' => 'hello']);
        $this->proxy->otherMethod(303);
        verify($this->proxy, 'otherMethod')->received(equals(303));
    }
}
