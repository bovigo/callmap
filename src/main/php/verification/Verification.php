<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap\verification;

use bovigo\assert\phpunit\ConstraintAdapter;
use bovigo\assert\predicate\Predicate;
use \bovigo\callmap\Invocations;
use ReflectionClass;
use RuntimeException;

use function bovigo\assert\predicate\equals;
/**
 * Provides methods to verify that a method or function was called an exact amount of times.
 *
 * This class is in a separate namespace so that it can be safely excluded from PHPUnit
 * error stacks. Unfortunately, the exclude feature of PHPUnit allows for adding hole
 * directories only.
 *
 * @since 0.5.0
 */
class Verification
{
    /**
     * constructor
     *
     * @internal use bovigo\callmap\verify() instead
     */
    public function __construct(private Invocations $invocations) { }

    /**
     * adds predicate count as constraint count to PHPUnit if present
     *
     * This is definitely a hack and might break with future PHPUnit releases.
     *
     * @internal
     * @staticvar \ReflectionProperty $property
     */
    private function increaseAssertionCounter(): void
    {
        static $property = null;
        if (null === $property && class_exists('\PHPUnit\Framework\Assert')) {
            $assertClass = new ReflectionClass(\PHPUnit\Framework\Assert::class);
            $property = $assertClass->getProperty('count');
            $property->setAccessible(true);
        }

        if (null !== $property) {
            $property->setValue(null, $property->getValue() + 1);
        }
    }

    /**
     * verifies that the method on the class was not called more than $times
     *
     * @api
     * @throws CallAmountViolation
     */
    public function wasCalledAtMost(int $times): bool
    {
        $this->increaseAssertionCounter();
        if (count($this->invocations) > $times) {
            throw new CallAmountViolation(sprintf(
                '%s was expected to be called at most %d time(s),'
                . ' but actually called %d time(s).',
                $this->invocations->name(),
                $times,
                count($this->invocations)
            ));
        }

        return true;
    }

    /**
     * verifies that the method on the class was called at least once
     *
     * @api
     * @throws CallAmountViolation
     */
    public function wasCalledAtLeastOnce(): bool
    {
        $this->increaseAssertionCounter();
        if (count($this->invocations) < 1) {
            throw new CallAmountViolation(sprintf(
                '%s was expected to be called at least once,'
                . ' but was never called.',
                $this->invocations->name()
            ));
        }

        return true;
    }

    /**
     * verifies that the method on the class was called at least $times
     *
     * @api
     * @throws CallAmountViolation
     */
    public function wasCalledAtLeast(int $times): bool
    {
        $this->increaseAssertionCounter();
        if (count($this->invocations) < $times) {
            throw new CallAmountViolation(sprintf(
                '%s was expected to be called at least %d time(s),'
                . ' but actually called %d time(s).',
                $this->invocations->name(),
                $times,
                count($this->invocations)
            ));
        }

        return true;
    }

    /**
     * verifies that the method on the class was called exactly once
     *
     * @api
     * @throws CallAmountViolation
     */
    public function wasCalledOnce(): bool
    {
        $this->increaseAssertionCounter();
        $callsReceived = count($this->invocations);
        if (1 !== $callsReceived) {
            throw new CallAmountViolation(sprintf(
                '%s was expected to be called once, but actually %s.',
                $this->invocations->name(),
                1 < $callsReceived ?
                    'called ' . $callsReceived . ' time(s)' :
                    'never called'

            ));
        }

        return true;
    }

    /**
     * verifies that the method on the class was called exactly $times
     *
     * @api
     * @throws CallAmountViolation
     */
    public function wasCalled(int $times): bool
    {
        $this->increaseAssertionCounter();
        if (count($this->invocations) != $times) {
            throw new CallAmountViolation(sprintf(
                '%s was expected to be called %d time(s),'
                . ' but actually called %d time(s).',
                $this->invocations->name(),
                $times,
                count($this->invocations)
            ));
        }

        return true;
    }

    /**
     * verifies that the method on the class was never called
     *
     * @api
     * @throws CallAmountViolation
     */
    public function wasNeverCalled(): bool
    {
        $this->increaseAssertionCounter();
        if (count($this->invocations) > 0) {
            throw new CallAmountViolation(sprintf(
                '%s was not expected to be called,'
                . ' but actually called %d time(s).',
                $this->invocations->name(),
                count($this->invocations)
            ));
        }

        return true;
    }

    /**
     * verifies that the method received nothing on the given invocation
     *
     * @api
     * @throws ArgumentMismatch
     */
    public function receivedNothing(int $invocation = 1): bool
    {
        $this->increaseAssertionCounter();
        $received = $this->invocations->argumentsOf($invocation);
        if  (count($received) === 0) {
            return true;
        }

        throw new ArgumentMismatch(sprintf(
            'Argument count for invocation #%d of %s is too'
            . ' high: received %d argument(s), expected no arguments.',
            $invocation,
            $this->invocations->name(),
            count($received)
        ));
    }

    /**
     * verifies that the received arguments match expected arguments for the first invocation
     *
     * If a constraint is not an instance of PHPUnit\Framework\Constraint\Constraint it
     * will automatically use PHPUnit\Framework\Constraint\IsEqual.
     *
     * @api
     * @param   mixed|Predicate|\PHPUnit\Framework\Constraint\Constraint[]  ...$expected  constraints which describe expected parameters
     * @return  bool
     */
    public function received(mixed ...$expected): bool
    {
        return $this->verifyArgs(1, $expected);
    }

    /**
     * verifies that the received arguments match expected arguments for the given invocation
     *
     * If a constraint is not an instance of PHPUnit\Framework\Constraint\Constraint it
     * will automatically use PHPUnit\Framework\Constraint\IsEqual.
     *
     * @api
     * @param  int                                                        $invocation  nth invocation to check
     * @param  mixed|Predicate|\PHPUnit\Framework\Constraint\Constraint[] ...$expected constraints which describe expected parameters
     * @return bool
     */
    public function receivedOn(int $invocation, mixed ...$expected): bool
    {
        return $this->verifyArgs($invocation, $expected);
    }

    /**
     * verifies arguments of given invocation with the expected constraints
     *
     * If a constraint is not an instance of PHPUnit\Framework\Constraint\Constraint it
     * will automatically use PHPUnit\Framework\Constraint\IsEqual.
     *
     * @param  int          $invocation number of invocation to check
     * @param  array<mixed> $expected   constraints which describe expected parameters
     * @return bool
     * @throws ArgumentMismatch
     */
    private function verifyArgs(int $invocation, array $expected): bool
    {
        $received = $this->invocations->argumentsOf($invocation);
        if (count($received) < count($expected)) {
            throw new ArgumentMismatch(sprintf(
                'Argument count for invocation #%d of %s is too'
                . ' low: received %d argument(s), expected %d argument(s).',
                $invocation,
                $this->invocations->name(),
                count($received),
                count($expected)
            ));
        }

        foreach ($expected as $atPosition => $constraint) {
            $this->evaluate(
                $constraint,
                $received[$atPosition] ?? null,
                sprintf(
                    'Parameter %sat position %d for invocation #%d of %s'
                    . ' does not match expected value.',
                    $this->invocations->argumentName($atPosition, ' '),
                    $atPosition,
                    $invocation,
                    $this->invocations->name()
                )
            );
        }

        return true;
    }

    /**
     * evaluates given constraint given received argument
     *
     * @param  mixed|Predicate|\PHPUnit\Framework\Constraint\Constraint $constraint  constraint for argument
     * @param  mixed                                                    $received    actually received argument
     * @param  string                                                   $description description for invocation in case of error
     * @return bool
     * @throws RuntimeException in case neither bovigo/assert, PHPUnit not xp-framework/unittest is present
     */
    private function evaluate(
        mixed $constraint,
        mixed $received,
        string $description
    ): bool {
        if (function_exists('bovigo\assert\assertThat')) {
            return \bovigo\assert\assertThat(
                $received,
                $this->predicateFor($constraint),
                $description
            );
        }

        if (class_exists('\PHPUnit\Framework\Constraint\IsEqual')) {
            return $this->evaluateWithPhpUnit($constraint, $received, $description);
        }

        if (class_exists('\unittest\TestCase')) {
            return $this->evaluateWithXpFrameworkCore($constraint, $received, $description);
        }

        throw new RuntimeException('Neither bovigo/assert, PHPUnit nor xp-framework/unittest found, can not perform argument verification');
    }

    /**
     * creates precicate for given constraint
     *
     * @param  mixed|Predicate|\PHPUnit\Framework\Constraint\Constraint $constraint
     * @return Predicate
     */
    private function predicateFor(mixed $constraint): Predicate
    {
        if ($constraint instanceof \PHPUnit\Framework\Constraint\Constraint) {
            return new ConstraintAdapter($constraint);
        }

        if ($constraint instanceof Predicate) {
            return $constraint;
        }

        return equals($constraint);
    }

    /**
     * evaluates given constraint using PHPUnit
     *
     * If given constraint is not a instance of \PHPUnit\Framework\Constraint\Constraint it
     * will be wrapped with \PHPUnit\Framework\Constraint\IsEqual.
     *
     * @param  mixed|\PHPUnit\Framework\Constraint\Constraint $constraint  constraint for argument
     * @param  mixed                                          $received    actually received argument
     * @param  string                                         $description description for invocation in case of error
     * @return bool
     */
    protected function evaluateWithPhpUnit(
        mixed $constraint,
        mixed $received,
        string $description
    ): bool {
        if ($constraint instanceof \PHPUnit\Framework\Constraint\Constraint) {
            $result = $constraint->evaluate($received, $description);
        } else {
            $result = (new \PHPUnit\Framework\Constraint\IsEqual($constraint))
                ->evaluate($received, $description);
        }

        // PHPUnit constraints return null when no return value is requested with third
        // parameter, but return value can't be requested when you want the constraint
        // to throw on failure like we do
        // Therefore, if the evaluate() method doesn't throw and returns we can assume
        // success, even if the result value is null.
        if (null === $result) {
            $result = true;
        }

        return $result;
    }

    /**
     * evaluates given constraint using xp-framework/core unittest
     *
     * @throws \unittest\AssertionFailedError
     */
    protected function evaluateWithXpFrameworkCore(
        mixed $constraint,
        mixed $received,
        string $description
    ): bool {
        if (!\util\Objects::equal($received, $constraint)) {
            throw new \unittest\AssertionFailedError(
                new \unittest\ComparisonFailedMessage(
                    $description,
                    $constraint,
                    $received
                )
            );
        }

        return true;
    }
}
