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

use bovigo\callmap\verification\ArgumentMismatch;
use bovigo\callmap\verification\CallAmountViolation;
use bovigo\callmap\verification\Verification;
use bovigo\callmap\helper\Verified;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for bovigo\callmap\verify() with class proxy.
 *
 * @since 0.5.0
 */
#[Group('verify')]
class VerifyClassProxyTest extends TestCase
{
    private Verified&ClassProxy $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::of(Verified::class);
    }

    public static function verificationMethods(): Generator
    {
        yield 'wasNeverCalled' => [fn(Verification $v, Verified $proxy) => $v->wasNeverCalled()];
        yield 'wasCalledAtMost' => [fn(Verification $v, Verified $proxy) => $proxy->aMethod() && $v->wasCalledAtMost(1)];
        yield 'wasCalledAtLeastOnce' => [fn(Verification $v, Verified $proxy) => $proxy->aMethod() && $v->wasCalledAtLeastOnce()];
        yield 'wasCalledAtLeast' => [fn(Verification $v, Verified $proxy) => $proxy->aMethod() && $v->wasCalledAtLeast(1)];
        yield 'wasCalledOnce' => [fn(Verification $v, Verified $proxy) => $proxy->aMethod() && $v->wasCalledOnce()];
        yield 'wasCalled' => [fn(Verification $v, Verified $proxy) => $proxy->aMethod() && $v->wasCalled(1)];
        yield 'receivedNothing' => [fn(Verification $v, Verified $proxy) => $proxy->aMethod() && $v->receivedNothing()];
    }

    /**
     * @since 6.1.0
     */
    #[Test]
    #[DataProvider('verificationMethods')]
    public function assertionCounterIsIncreased(callable $execute): void
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
     * @since 3.1.0
     */
    #[Test]
    public function verifyWithoutMethodNameThrowsInvalidArgumentException(): void
    {
        expect(fn() => verify($this->proxy))
            ->throws(InvalidArgumentException::class)
            ->withMessage('Please provide a method name to retrieve invocations for.');
    }

    #[Test]
    public function wasNeverCalledReturnsTrueWhenNeverCalled(): void
    {
        verify($this->proxy, 'aMethod')->wasNeverCalled();
    }

    #[Test]
    public function wasNeverCalledThrowsCallAmountViolationWhenMethodWasCalled(): void
    {
        $this->proxy->aMethod();
        expect(fn() => verify($this->proxy, 'aMethod')->wasNeverCalled())
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was not expected to'
                . ' be called, but actually called 1 time(s).'
            );
    }

    #[Test]
    public function wasCalledReturnsTrueWhenCalledExactlyWithGivenAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalled(2);
    }

    #[Test]
    public function wasCalledThrowsCallAmountViolationWhenCalledTooSeldom(): void
    {
        $this->proxy->aMethod();
        expect(fn() => verify($this->proxy, 'aMethod')->wasCalled(2))
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called 2 time(s), but actually called 1 time(s).'
            );
    }

    #[Test]
    public function wasCalledThrowsCallAmountViolationWhenCalledTooOften(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(fn() => verify($this->proxy, 'aMethod')->wasCalled(2))
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called 2 time(s), but actually called 3 time(s).'
            );
    }

    #[Test]
    public function wasCalledOnceReturnsTrueWhenCalledExactlyOnce(): void
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledOnce();
    }

    #[Test]
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledLessThanOnce(): void
    {
        expect(fn() => verify($this->proxy, 'aMethod')->wasCalledOnce())
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called once, but actually never called.'
            );
    }

    #[Test]
    public function wasCalledOnceThrowsCallAmountViolationWhenCalledMoreThanOnce(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(fn() => verify($this->proxy, 'aMethod')->wasCalledOnce())
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called once, but actually called 2 time(s).'
            );
    }

    #[Test]
    public function wasCalledAtLeastReturnsTrueWhenCalledExactlyMinimumAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeast(2);
    }

    #[Test]
    public function wasCalledAtLeastReturnsTrueWhenCalledMoreThanMinimumAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeast(2);
    }

    #[Test]
    public function wasCalledAtLeastThrowsCallAmountViolationWhenCalledLessThanMinimumAmount(): void
    {
        $this->proxy->aMethod();
        expect(fn() => verify($this->proxy, 'aMethod')->wasCalledAtLeast(2))
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called at least 2 time(s), but actually called 1 time(s).'
            );
    }

    #[Test]
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledExactlyOnce(): void
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce();
    }

    #[Test]
    public function wasCalledAtLeastOnceReturnsTrueWhenCalledMoreThanOnce(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce();
    }

    #[Test]
    public function wasCalledAtLeastOnceThrowsCallAmountViolationWhenCalledLessThanOnce(): void
    {
        expect(fn() => verify($this->proxy, 'aMethod')->wasCalledAtLeastOnce())
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called at least once, but was never called.'
            );
    }

    #[Test]
    public function wasCalledAtMostReturnsTrueWhenCalledExactlyMaximumAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtMost(2);
    }

    #[Test]
    public function wasCalledAtMostOnceReturnsTrueWhenCalledLessThanMaximumAmount(): void
    {
        $this->proxy->aMethod();
        verify($this->proxy, 'aMethod')->wasCalledAtMost(2);
    }

    #[Test]
    public function wasCalledAtMostOnceThrowsCallAmountViolationWhenCalledMoreThanMaximumAmount(): void
    {
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        $this->proxy->aMethod();
        expect(fn() => verify($this->proxy, 'aMethod')->wasCalledAtMost(2))
            ->throws(CallAmountViolation::class)
            ->withMessage(
                Verified::class . '::aMethod() was expected to be'
                . ' called at most 2 time(s), but actually called 3 time(s).'
            );
    }

    #[Test]
    public function verifyArgumentsForMethodNotCalledThrowsMissingInvocation(): void
    {
        expect(fn() => verify($this->proxy, 'aMethod')->receivedNothing())
            ->throws(MissingInvocation::class)
            ->withMessage(
                'Missing invocation #1 for ' . Verified::class . '::aMethod(),'
                . ' was never called.'
            );
    }

    #[Test]
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation6(): void
    {
        $this->proxy->aMethod(808);
        expect(fn() => verify($this->proxy, 'aMethod')->receivedOn(2, 808))
            ->throws(MissingInvocation::class)
            ->withMessage(
                'Missing invocation #2 for ' . Verified::class . '::aMethod(),'
                . ' was only called once.'
            );
    }

    #[Test]
    public function verifyArgumentsForMethodNotCalledThatManyTimesThrowsMissingInvocation(): void
    {
        $this->proxy->aMethod(808);
        $this->proxy->aMethod(808);
        expect(fn() => verify($this->proxy, 'aMethod')->receivedOn(3, 808))
            ->throws(MissingInvocation::class)
            ->withMessage(
                'Missing invocation #3 for ' . Verified::class . '::aMethod(),'
                . ' was only called 2 times.'
            );
    }

    #[Test]
    public function verifyReceivedNothingThrowsArgumentMismatchWhenArgumentsReceived(): void
    {
        $this->proxy->aMethod(808);
        expect(fn() => verify($this->proxy, 'aMethod')->receivedNothing())
            ->throws(ArgumentMismatch::class)
            ->withMessage(
                'Argument count for invocation #1 of ' . Verified::class . '::aMethod()'
                . ' is too high: received 1 argument(s), expected no arguments.'
            );
    }

    #[Test]
    public function verifyReceivedThrowsArgumentMismatchWhenLessArgumentsReceivedThanExpected(): void
    {
        $this->proxy->aMethod();
        expect(fn() => verify($this->proxy, 'aMethod')->received(808))
            ->throws(ArgumentMismatch::class)
            ->withMessage(
                'Argument count for invocation #1 of ' . Verified::class . '::aMethod()'
                . ' is too low: received 0 argument(s), expected 1 argument(s).'
            );
    }

    /**
     * @since 0.6.0
     */
    #[Test]
    public function verifyReceivedPassesExceptionThrownByConstraint(): void
    {
        $this->proxy->aMethod(303);
        expect(fn() => verify($this->proxy, 'aMethod')->received(808))
            ->throws(\PHPUnit\Framework\AssertionFailedError::class)
            ->withMessage(
                'Failed asserting that 303 is equal to 808.
Parameter $roland at position 0 for invocation #1 of ' . Verified::class . '::aMethod()'
                . ' does not match expected value.'
            );
    }

    /**
     * @since 2.0.0
     */
    #[Test]
    public function verifyWithPredicate(): void
    {
        $this->proxy->aMethod(303);
        verify($this->proxy, 'aMethod')->received(equals(303));
    }

    /**
     * @since 3.0.0
     */
    #[Test]
    public function verifyWithPhpUnitConstraint(): void
    {
        $this->proxy->aMethod(303);
        verify($this->proxy, 'aMethod')
            ->received(new \PHPUnit\Framework\Constraint\IsEqual(303));
    }

    /**
     * @since 3.0.0
     */
    #[Test]
    public function canVerifyArgumentsForNonMappedMethod(): void
    {
        $this->proxy->returns(['aMethod' => 'hello']);
        $this->proxy->otherMethod(303);
        verify($this->proxy, 'otherMethod')->received(equals(303));
    }
}
