<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
/**
 * Provides methods to verify that a method or function was called an exact amount of times.
 *
 * @since  0.5.0
 */
class Verification
{
    /**
     * @type  Invocations
     */
    private $invocations;

    /**
     * constructor
     *
     * @param  \bovigo\callmap\Invocations  $invocations
     * @internal  use bovigo\callmap\verify() instead
     */
    public function __construct(Invocations $invocations)
    {
        $this->invocations = $invocations;
    }

    /**
     * verifies that the method on the class was not called more than $times
     *
     * @api
     * @param   int  $times
     * @return  bool
     * @throws  \bovigo\callmap\CallAmountViolation
     */
    public function wasCalledAtMost(int $times): bool
    {
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
     * @return  bool
     * @throws  \bovigo\callmap\CallAmountViolation
     */
    public function wasCalledAtLeastOnce(): bool
    {
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
     * @param   int  $times
     * @return  bool
     * @throws  \bovigo\callmap\CallAmountViolation
     */
    public function wasCalledAtLeast(int $times): bool
    {
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
     * @return  bool
     * @throws  \bovigo\callmap\CallAmountViolation
     */
    public function wasCalledOnce(): bool
    {
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
     * @param   int  $times
     * @return  bool
     * @throws  \bovigo\callmap\CallAmountViolation
     */
    public function wasCalled(int $times): bool
    {
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
     * @return  bool
     * @throws  \bovigo\callmap\CallAmountViolation
     */
    public function wasNeverCalled(): bool
    {
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
     * @param   int  $invocation  optional  nth invocation to check, defaults to 1 aka first invocation
     * @return  bool
     * @throws  \bovigo\callmap\ArgumentMismatch
     */
    public function receivedNothing(int $invocation = 1): bool
    {
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
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit\Framework\Constraint\Constraint[]  ...$expected  constraints which describe expected parameters
     * @return  bool
     */
    public function received(...$expected): bool
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
     * @param   int                                                                       $invocation   nth invocation to check
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit\Framework\Constraint\Constraint[]  ...$expected  constraints which describe expected parameters
     * @return  bool
     */
    public function receivedOn(int $invocation, ...$expected): bool
    {
        return $this->verifyArgs($invocation, $expected);
    }

    /**
     * verifies arguments of given invocation with the expected constraints
     *
     * If a constraint is not an instance of PHPUnit\Framework\Constraint\Constraint it
     * will automatically use PHPUnit\Framework\Constraint\IsEqual.
     *
     * @param   int    $invocation  number of invocation to check
     * @param   array  $expected    constraints which describe expected parameters
     * @return  bool
     * @throws  \bovigo\callmap\ArgumentMismatch
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
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit\Framework\Constraint\Constraint  $constraint  constraint for argument
     * @param   mixed                                                                   $received    actually received argument
     * @param   string                                                                  $description  description for invocation in case of error
     * @return  bool
     * @throws  \RuntimeException  in case neither bovigo/assert, PHPUnit not xp-framework/unittest is present
     */
    private function evaluate($constraint, $received, string $description): bool
    {
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

        throw new \RuntimeException('Neither bovigo/assert, PHPUnit nor xp-framework/unittest found, can not perform argument verification');
    }

    /**
     * creates precicate for given constraint
     *
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit\Framework\Constraint\Constraint  $constraint
     * @return  \bovigo\assert\predicate\Predicate
     */
    private function predicateFor($constraint): \bovigo\assert\predicate\Predicate
    {
        if ($constraint instanceof \PHPUnit\Framework\Constraint\Constraint) {
            return new \bovigo\assert\phpunit\ConstraintAdapter($constraint);
        }

        if ($constraint instanceof \bovigo\assert\predicate\Predicate) {
            return $constraint;
        }

        return \bovigo\assert\predicate\equals($constraint);
    }

    /**
     * evaluates given constraint using PHPUnit
     *
     * If given constraint is not a instance of \PHPUnit\Framework\Constraint\Constraint it
     * will be wrapped with \PHPUnit\Framework\Constraint\IsEqual.
     *
     * @param   mixed|\PHPUnit\Framework\Constraint\Constraint  $constraint  constraint for argument
     * @param   mixed                                $received    actually received argument
     * @param   string                               $description  description for invocation in case of error
     * @return  bool
     */
    protected function evaluateWithPhpUnit($constraint, $received, string $description): bool
    {
        if ($constraint instanceof \PHPUnit\Framework\Constraint\Constraint) {
            return $constraint->evaluate($received, $description);
        }

        return (new \PHPUnit\Framework\Constraint\IsEqual($constraint))
                ->evaluate($received, $description);
    }

    /**
     * evaluates given constraint using xp-framework/core unittest
     *
     * @param   mixed   $constraint  constraint for argument
     * @param   mixed   $received    actually received argument
     * @param   string  $description  description for invocation in case of error
     * @return  bool
     * @throws  \unittest\AssertionFailedError
     */
    protected function evaluateWithXpFrameworkCore($constraint, $received, string $description): bool
    {
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
