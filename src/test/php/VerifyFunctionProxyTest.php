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
 * Helper function for the test.
 */
function say(string $whom)
{
    return 'Hello ' . $whom;
}
/**
 * Test for bovigo\callmap\verify() with function proxy.
 *
 * @since  3.1.0
 * @group  verify
 */
class VerifyFunctionProxyTest extends TestCase
{
    public function functionNames(): array
    {
        return [['strlen', '$str '], ['bovigo\callmap\say', '$whom ']];
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasNeverCalledReturnsTrueWhenNeverCalled($functionName)
    {
        $function = NewCallable::of($functionName);
        assertTrue(verify($function)->wasNeverCalled());
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasNeverCalledThrowsCallAmountViolationWhenFunctionWasCalled($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) {
            verify($function)->wasNeverCalled();
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        $functionName . ' was not expected to be called, but'
                        . ' actually called 1 time(s).'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledReturnsTrueWhenCalledExactlyWithGivenAmount($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        assertTrue(verify($function)->wasCalled(2));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooSeldom($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) {
                verify($function)->wasCalled(2);
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        $functionName . ' was expected to be called 2 time(s),'
                        . ' but actually called 1 time(s).'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooOften($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        $function('world');
        expect(function() use ($function) {
                verify($function)->wasCalled(2);
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        $functionName . ' was expected to be called 2 time(s),'
                        . ' but actually called 3 time(s).'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledOnceReturnsTrueWhenCalledExactlyOnce($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        assertTrue(verify($function)->wasCalledOnce());
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledLessThanOnce($functionName)
    {
        $function = NewCallable::of($functionName);
        expect(function() use ($function) {
                verify($function)->wasCalledOnce();
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        $functionName . ' was expected to be called once'
                        . ', but actually never called.'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledMoreThanOnce($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        expect(function() use ($function) {
                verify($function)->wasCalledOnce();
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        $functionName . ' was expected to be called once,'
                        . ' but actually called 2 time(s).'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastReturnsTrueWhenCalledExactlyMinimumAmount($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        assertTrue(verify($function)->wasCalledAtLeast(2));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastReturnsTrueWhenCalledMoreThanMinimumAmount($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        $function('world');
        assertTrue(verify($function)->wasCalledAtLeast(2));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastThrowsCallAmountViolationWhenCalledLessThanMinimumAmount($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) {
                verify($function)->wasCalledAtLeast(2);
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        $functionName . ' was expected to be called at least 2'
                        . ' time(s), but actually called 1 time(s).'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledExactlyOnce($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        assertTrue(verify($function)->wasCalledAtLeastOnce());
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledMoreThanOnce($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        assertTrue(verify($function)->wasCalledAtLeastOnce());
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastOnceThrowsCallAmountViolationWhenCalledLessThanOnce($functionName)
    {
        $function = NewCallable::of($functionName);
        expect(function() use ($function) {
                verify($function)->wasCalledAtLeastOnce();
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        $functionName . ' was expected to be called at least'
                        . ' once, but was never called.'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtMostReturnsTrueWhenCalledExactlyMaximumAmount($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        assertTrue(verify($function)->wasCalledAtMost(2));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtMostOnceReturnsTrueWhenCalledLessThanMaximumAmount($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        assertTrue(verify($function)->wasCalledAtMost(2));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtMostOnceThrowsCallAmountViolationWhenCalledMoreThanMaximumAmount($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        $function('world');
        expect(function() use ($function) {
                verify($function)->wasCalledAtMost(2);
        })
                ->throws(CallAmountViolation::class)
                ->withMessage(
                        $functionName . ' was expected to be called at most'
                        . ' 2 time(s), but actually called 3 time(s).'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyArgumentsForMethodNotCalledThrowsMissingInvocation($functionName)
    {
        $function = NewCallable::of($functionName);
        expect(function() use ($function) {
                verify($function)->receivedNothing();
        })
                ->throws(MissingInvocation::class)
                ->withMessage(
                        'Missing invocation #1 for ' . $functionName
                        . ', was never called.'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation6($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function()  use ($function) {
                verify($function)->receivedOn(2, 808);
        })
                ->throws(MissingInvocation::class)
                ->withMessage(
                        'Missing invocation #2 for ' . $functionName
                        . ', was only called once.');
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        expect(function()  use ($function) {
                verify($function)->receivedOn(3, 808);
        })
                ->throws(MissingInvocation::class)
                ->withMessage(
                        'Missing invocation #3 for ' . $functionName
                        . ', was only called 2 times.'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyReceivedNothingThrowsArgumentMismatchWhenArgumentsReceived($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) {
                verify($function)->receivedNothing();
        })
                ->throws(ArgumentMismatch::class)
                ->withMessage(
                        'Argument count for invocation #1 of ' . $functionName
                        . ' is too high: received 1 argument(s), expected no arguments.'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyReceivedThrowsArgumentMismatchWhenLessArgumentsReceivedThanExpected($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) {
                verify($function)->received('world', 808);
        })
                ->throws(ArgumentMismatch::class)
                ->withMessage(
                        'Argument count for invocation #1 of ' . $functionName
                        . ' is too low: received 1 argument(s), expected 2 argument(s).'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyReceivedPassesExceptionThrownByConstraint($functionName, $parameterName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) {
                verify($function)->received(808);
        })
                ->throws(\PHPUnit\Framework\AssertionFailedError::class)
                ->withMessage(
                    'Failed asserting that \'world\' is equal to 808.
Parameter ' . $parameterName . 'at position 0 for invocation #1 of ' . $functionName
                    . ' does not match expected value.'
                );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyWithPredicate($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        verify($function)->received(equals('world'));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyWithPhpUnitConstraint($functionName)
    {
        $function = NewCallable::of($functionName);
        $function('world');
        verify($function)->received(
                new \PHPUnit\Framework\Constraint\IsEqual('world')
        );
    }
}
