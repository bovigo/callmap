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
use bovigo\callmap\helper\Verified;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for bovigo\callmap\verify() with class proxy.
 *
 * @since  0.5.0
 * @group  verify
 */
class VerifyClassProxyTest extends TestCase
{
    /**
     * @var  Verified&\bovigo\callmap\ClassProxy
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
     * @return  array<string,\Closure[]>
     */
    public static function verificationMethods(): array
    {
        return [
            'wasNeverCalled' => [function(Verification $v, Verified $proxy) { $v->wasNeverCalled(); }],
            'wasCalledAtMost' => [function(Verification $v, Verified $proxy) { $proxy->aMethod(); $v->wasCalledAtMost(1); }],
            'wasCalledAtLeastOnce' => [function(Verification $v, Verified $proxy) { $proxy->aMethod(); $v->wasCalledAtLeastOnce(); }],
            'wasCalledAtLeast' => [function(Verification $v, Verified $proxy) { $proxy->aMethod(); $v->wasCalledAtLeast(1); }],
            'wasCalledOnce' => [function(Verification $v, Verified $proxy) { $proxy->aMethod(); $v->wasCalledOnce(); }],
            'wasCalled' => [function(Verification $v, Verified $proxy) { $proxy->aMethod(); $v->wasCalled(1); }],
            'receivedNothing' => [function(Verification $v, Verified $proxy) { $proxy->aMethod(); $v->receivedNothing(); }],
        ];
    }

    /**
     * @test
     * @dataProvider  verificationMethods
     * @since  6.1.0
     */
    public function assertionCounterIsIncreased(\Closure $execute): void
    {
        if (!class_exists('\PHPUnit\Framework\Assert')) {
            $this->markTestSkipped('Can not test this without PHPUnit');
        }

        $countBeforeAssertion = \PHPUnit\Framework\Assert::getCount();
        $execute(verify($this->proxy, 'aMethod'), $this->proxy);
        assertThat(
                \PHPUnit\Framework\Assert::getCount(),
                equals($countBeforeAssertion + 1)
        );
    }

    /**
     * @test
     * @since  3.1.0
     */
    public function verifyWithoutMethodNameThrowsInvalidArgumentException(): void
    {
        expect(function() { verify($this->proxy); })
            ->throws(\InvalidArgumentException::class)
            ->withMessage('Please provide a method name to retrieve invocations for.');
    }

    /**
     * @test
     */
    public function wasNeverCalledReturnsTrueWhenNeverCalled(): void
    {
        verify($this->proxy, 'aMethod')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function wasNeverCalledThrowsCallAmountViolationWhenMethodWasCalled(): void
    {
        $this->proxy->aMethod();
        expect(function() { verify($this->proxy, 'aMethod')->wasNeverCalled(); })
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was not expected to'
                . ' be called, but actually called 1 time(s).'
            );
    }

    /**
     * @test
     */
    public function wasCalledReturnsTrueWhenCalledExactlyWithGivenAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalled(2);
    }

    /**
     * @test
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooSeldom(): void
    {
        $this->proxy->aMethod();
        expect(function() { verify($this->proxy, 'aMethod')->wasCalled(2); })
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called 2 time(s), but actually called 1 time(s).'
            );
    }

    /**
     * @test
     */
    public function wasCalledThrowsCallAmountViolationWhenCalledTooOften(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(function() { verify($this->proxy, 'aMethod')->wasCalled(2); })
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called 2 time(s), but actually called 3 time(s).'
            );
    }

    /**
     * @test
     */
    public function wasCalledOnceReturnsTrueWhenCalledExactlyOnce(): void
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledLessThanOnce(): void
    {
        expect(function() { verify($this->proxy, 'aMethod')->wasCalledOnce(); })
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called once, but actually never called.'
            );
    }

    /**
     * @test
     */
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledMoreThanOnce(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(function() { verify($this->proxy, 'aMethod')->wasCalledOnce(); })
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called once, but actually called 2 time(s).'
            );
    }

    /**
     * @test
     */
    public function wasCalledAtLeastReturnsTrueWhenCalledExactlyMinimumAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeast(2);
    }

    /**
     * @test
     */
    public function wasCalledAtLeastReturnsTrueWhenCalledMoreThanMinimumAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeast(2);
    }

    /**
     * @test
     */
    public function wasCalledAtLeastThrowsCallAmountViolationWhenCalledLessThanMinimumAmount(): void
    {
        $this->proxy->aMethod();
        expect(function() { verify($this->proxy, 'aMethod')->wasCalledAtLeast(2); })
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called at least 2 time(s), but actually called 1 time(s).'
            );
    }

    /**
     * @test
     */
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledExactlyOnce(): void
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce();
    }

    /**
     * @test
     */
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledMoreThanOnce(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce();
    }

    /**
     * @test
     */
    public function wasCalledAtLeastOnceThrowsCallAmountViolationWhenCalledLessThanOnce(): void
    {
        expect(function() { verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce(); })
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called at least once, but was never called.'
            );
    }

    /**
     * @test
     */
    public function wasCalledAtMostReturnsTrueWhenCalledExactlyMaximumAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtMost(2);
    }

    /**
     * @test
     */
    public function wasCalledAtMostOnceReturnsTrueWhenCalledLessThanMaximumAmount(): void
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtMost(2);
    }

    /**
     * @test
     */
    public function wasCalledAtMostOnceThrowsCallAmountViolationWhenCalledMoreThanMaximumAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(function() { verify($this->proxy, 'aMethod')->wasCalledAtMost(2); })
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called at most 2 time(s), but actually called 3 time(s).'
            );
    }

    /**
     * @test
     */
    public function verifyArgumentsForMethodNotCalledThrowsMissingInvocation(): void
    {
        expect(function() { verify($this->proxy, 'aMethod')->receivedNothing(); })
            ->throws(MissingInvocation::class)
            ->withMessage(
                'Missing invocation #1 for ' . Verified::class . '::aMethod(),'
                . ' was never called.'
            );
    }

    /**
     * @test
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation6(): void
    {
        $this->proxy->aMethod(808);
        expect(function() { verify($this->proxy, 'aMethod')->receivedOn(2, 808); })
            ->throws(MissingInvocation::class)
            ->withMessage(
                'Missing invocation #2 for ' . Verified::class . '::aMethod(),'
                . ' was only called once.'
            );
    }

    /**
     * @test
     */
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation(): void
    {
        $this->proxy->aMethod(808);
        $this->proxy->aMethod(808);
        expect(function() { verify($this->proxy, 'aMethod')->receivedOn(3, 808); })
            ->throws(MissingInvocation::class)
            ->withMessage(
                'Missing invocation #3 for ' . Verified::class . '::aMethod(),'
                . ' was only called 2 times.'
            );
    }

    /**
     * @test
     */
    public function verifyReceivedNothingThrowsArgumentMismatchWhenArgumentsReceived(): void
    {
        $this->proxy->aMethod(808);
        expect(function() { verify($this->proxy, 'aMethod')->receivedNothing(); })
            ->throws(ArgumentMismatch::class)
            ->withMessage(
                'Argument count for invocation #1 of ' . Verified::class . '::aMethod()'
                . ' is too high: received 1 argument(s), expected no arguments.'
            );
    }

    /**
     * @test
     */
    public function verifyReceivedThrowsArgumentMismatchWhenLessArgumentsReceivedThanExpected(): void
    {
        $this->proxy->aMethod();
        expect(function() { verify($this->proxy, 'aMethod')->received(808); })
            ->throws(ArgumentMismatch::class)
            ->withMessage(
                'Argument count for invocation #1 of ' . Verified::class . '::aMethod()'
                . ' is too low: received 0 argument(s), expected 1 argument(s).'
            );
    }

    /**
     * @test
     * @since  0.6.0
     */
    public function verifyReceivedPassesExceptionThrownByConstraint(): void
    {
        $this->proxy->aMethod(303);
        expect(function() { verify($this->proxy, 'aMethod')->received(808); })
            ->throws(\PHPUnit\Framework\AssertionFailedError::class)
            ->withMessage(
                'Failed asserting that 303 is equal to 808.
Parameter $roland at position 0 for invocation #1 of ' . Verified::class . '::aMethod()'
                . ' does not match expected value.'
            );
    }

    /**
     * @test
     * @since  2.0.0
     */
    public function verifyWithPredicate(): void
    {
        $this->proxy->aMethod(303);
        verify($this->proxy, 'aMethod')->received(equals(303));
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function verifyWithPhpUnitConstraint(): void
    {
        $this->proxy->aMethod(303);
        verify($this->proxy, 'aMethod')
            ->received(new \PHPUnit\Framework\Constraint\IsEqual(303));
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function canVerifyArgumentsForNonMappedMethod(): void
    {
        $this->proxy->returns(['aMethod' => 'hello']);
        $this->proxy->otherMethod(303);
        verify($this->proxy, 'otherMethod')->received(equals(303));
    }
}
