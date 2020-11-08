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
use \bovigo\callmap\verification\ArgumentMismatch;
use \bovigo\callmap\verification\CallAmountViolation;
use \bovigo\callmap\verification\Verification;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\helper\say;
/**
 * Test for bovigo\callmap\verify() with function proxy.
 *
 * @since  3.1.0
 * @group  verify
 */
class VerifyFunctionProxyTest extends TestCase
{

    /**
     * @return  array<string,\Closure[]>
     */
    public static function verificationMethods(): array
    {
        return [
            'wasNeverCalled' => [function(Verification $v, callable $function) { $v->wasNeverCalled(); }],
            'wasCalledAtMost' => [function(Verification $v, callable $function) { $function(); $v->wasCalledAtMost(1); }],
            'wasCalledAtLeastOnce' => [function(Verification $v, callable $function) { $function(); $v->wasCalledAtLeastOnce(); }],
            'wasCalledAtLeast' => [function(Verification $v, callable $function) { $function(); $v->wasCalledAtLeast(1); }],
            'wasCalledOnce' => [function(Verification $v, callable $function) { $function(); $v->wasCalledOnce(); }],
            'wasCalled' => [function(Verification $v, callable $function) { $function(); $v->wasCalled(1); }],
            'receivedNothing' => [function(Verification $v, callable $function) { $function(); $v->receivedNothing(); }],
        ];
    }

    /**
     * @test
     * @dataProvider  verificationMethods
     * @since  6.1.0
     */
    public function assertionCounterIsIncreased(\Closure $execute): void
    {
        $countBeforeAssertion = \PHPUnit\Framework\Assert::getCount();
        $function = NewCallable::of('bovigo\callmap\helper\doSomething');
        $execute(verify($function), $function);
        assertThat(
                \PHPUnit\Framework\Assert::getCount(),
                equals($countBeforeAssertion + 1)
        );
    }

    /**
     * @return  array<string[]>
     */
    public function functionNames(): array
    {
        return [['strlen', '$str '], ['bovigo\callmap\helper\say', '$whom ']];
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasNeverCalledReturnsTrueWhenNeverCalled(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        verify($function)->wasNeverCalled();
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasNeverCalledThrowsCallAmountViolationWhenFunctionWasCalled(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) { verify($function)->wasNeverCalled(); })
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
    public function wasCalledReturnsTrueWhenCalledExactlyWithGivenAmount(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        verify($function)->wasCalled(2);
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooSeldom(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) { verify($function)->wasCalled(2); })
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
    public function wasCalledThrowsCallAmountViolationWhenCalledTooOften(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        $function('world');
        expect(function() use ($function) { verify($function)->wasCalled(2); })
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
    public function wasCalledOnceReturnsTrueWhenCalledExactlyOnce(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        verify($function)->wasCalledOnce();
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledLessThanOnce(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        expect(function() use ($function) { verify($function)->wasCalledOnce(); })
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
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledMoreThanOnce(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        expect(function() use ($function) { verify($function)->wasCalledOnce(); })
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
    public function wasCalledAtLeastReturnsTrueWhenCalledExactlyMinimumAmount(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        verify($function)->wasCalledAtLeast(2);
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastReturnsTrueWhenCalledMoreThanMinimumAmount(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        $function('world');
        verify($function)->wasCalledAtLeast(2);
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastThrowsCallAmountViolationWhenCalledLessThanMinimumAmount(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) { verify($function)->wasCalledAtLeast(2); })
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
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledExactlyOnce(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        verify($function)->wasCalledAtLeastOnce();
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledMoreThanOnce(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        verify($function)->wasCalledAtLeastOnce();
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtLeastOnceThrowsCallAmountViolationWhenCalledLessThanOnce(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        expect(function() use ($function) { verify($function)->wasCalledAtLeastOnce(); })
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
    public function wasCalledAtMostReturnsTrueWhenCalledExactlyMaximumAmount(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        verify($function)->wasCalledAtMost(2);
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtMostOnceReturnsTrueWhenCalledLessThanMaximumAmount(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        verify($function)->wasCalledAtMost(2);
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function wasCalledAtMostOnceThrowsCallAmountViolationWhenCalledMoreThanMaximumAmount(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        $function('world');
        expect(function() use ($function) { verify($function)->wasCalledAtMost(2); })
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
    public function verifyArgumentsForMethodNotCalledThrowsMissingInvocation(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        expect(function() use ($function) { verify($function)->receivedNothing(); })
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
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation6(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function()  use ($function) { verify($function)->receivedOn(2, 808); })
          ->throws(MissingInvocation::class)
          ->withMessage(
              'Missing invocation #2 for ' . $functionName
              . ', was only called once.'
          );
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        $function('world');
        expect(function()  use ($function) { verify($function)->receivedOn(3, 808); })
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
    public function verifyReceivedNothingThrowsArgumentMismatchWhenArgumentsReceived(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) { verify($function)->receivedNothing(); })
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
    public function verifyReceivedThrowsArgumentMismatchWhenLessArgumentsReceivedThanExpected(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) { verify($function)->received('world', 808); })
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
    public function verifyReceivedPassesExceptionThrownByConstraint(string $functionName, string $parameterName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        expect(function() use ($function) { verify($function)->received(808); })
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
    public function verifyWithPredicate(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        verify($function)->received(equals('world'));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function verifyWithPhpUnitConstraint(string $functionName): void
    {
        $function = NewCallable::of($functionName);
        $function('world');
        verify($function)->received(
            new \PHPUnit\Framework\Constraint\IsEqual('world')
        );
    }
}
