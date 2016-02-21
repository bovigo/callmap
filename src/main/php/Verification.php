<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
/**
 * Provides methods to verify that a method was called an exact amount of times.
 *
 * @since  0.5.0
 */
class Verification
{
    /**
     * callmap to verify method call amount of
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $callmap;
    /**
     * actual method to verify
     *
     * @type  string
     */
    private $method;

    /**
     * constructor
     *
     * @param  \bovigo\callmap\Proxy  $callmap  callmap to verify method call amount of
     * @param  string                 $method   actual method to verify
     * @internal  use bovigo\callmap\verify() instead
     */
    public function __construct(Proxy $callmap, $method)
    {
        $this->callmap = $callmap;
        $this->method  = $method;
    }

    /**
     * verifies that the method on the class was not called more than $times
     *
     * @api
     * @param   int  $times
     * @return  bool
     * @throws  \bovigo\callmap\CallAmountViolation
     */
    public function wasCalledAtMost($times)
    {
        if ($this->callmap->callsReceivedFor($this->method) > $times) {
            throw new CallAmountViolation(
                    sprintf(
                            '%s was expected to be called at most %d time(s),'
                            . ' but actually called %d time(s).',
                            methodName($this->callmap, $this->method),
                            $times,
                            $this->callmap->callsReceivedFor($this->method)
                    )
            );
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
    public function wasCalledAtLeastOnce()
    {
        if ($this->callmap->callsReceivedFor($this->method) < 1) {
            throw new CallAmountViolation(
                    sprintf(
                            '%s was expected to be called at least once,'
                            . ' but was never called.',
                            methodName($this->callmap, $this->method)
                    )
            );
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
    public function wasCalledAtLeast($times)
    {
        if ($this->callmap->callsReceivedFor($this->method) < $times) {
            throw new CallAmountViolation(
                    sprintf(
                            '%s was expected to be called at least %d time(s),'
                            . ' but actually called %d time(s).',
                            methodName($this->callmap, $this->method),
                            $times,
                            $this->callmap->callsReceivedFor($this->method)
                    )
            );
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
    public function wasCalledOnce()
    {
        $callsReceived = $this->callmap->callsReceivedFor($this->method);
        if (1 !== $callsReceived) {
            throw new CallAmountViolation(
                    sprintf(
                            '%s was expected to be called once, but actually %s.',
                            methodName($this->callmap, $this->method),
                            1 < $callsReceived ?
                                'called ' . $callsReceived . ' time(s)' :
                                'never called'
                    )
            );
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
    public function wasCalled($times)
    {
        if ($this->callmap->callsReceivedFor($this->method) != $times) {
            throw new CallAmountViolation(
                    sprintf(
                            '%s was expected to be called %d time(s),'
                            . ' but actually called %d time(s).',
                            methodName($this->callmap, $this->method),
                            $times,
                            $this->callmap->callsReceivedFor($this->method)
                    )
            );
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
    public function wasNeverCalled()
    {
        if ($this->callmap->callsReceivedFor($this->method) > 0) {
            throw new CallAmountViolation(
                    sprintf(
                            '%s was not expected to be called,'
                            . ' but actually called %d time(s).',
                            methodName($this->callmap, $this->method),
                            $this->callmap->callsReceivedFor($this->method)
                    )
            );
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
    public function receivedNothing($invocation = 1)
    {
        $received = $this->callmap->argumentsReceivedFor($this->method, $invocation);
        if  (count($received['arguments']) === 0) {
            return true;
        }

        throw new ArgumentMismatch(
                sprintf(
                    'Argument count for invocation #%d of %s is too'
                    . ' high: received %d argument(s), expected no arguments.',
                    $invocation,
                    methodName($this->callmap, $this->method),
                    count($received['arguments'])
                )
        );
    }

    /**
     * verifies that the received arguments match expected arguments for the first invocation
     *
     * If a constraint is not an instance of PHPUnit_Framework_Constraint it
     * will automatically use PHPUnit_Framework_Constraint_IsEqual.
     *
     * @api
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit_Framework_Constraint[]  ...$expected  constraints which describe expected parameters
     * @return  bool
     */
    public function received(...$expected)
    {
        return $this->verifyArgs(1, $expected);
    }

    /**
     * verifies that the received arguments match expected arguments for the given invocation
     *
     * If a constraint is not an instance of PHPUnit_Framework_Constraint it
     * will automatically use PHPUnit_Framework_Constraint_IsEqual.
     *
     * @api
     * @param   int                                                                       $invocation   nth invocation to check
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit_Framework_Constraint[]  ...$expected  constraints which describe expected parameters
     * @return  bool
     */
    public function receivedOn($invocation, ...$expected)
    {
        return $this->verifyArgs($invocation, $expected);
    }

    /**
     * verifies arguments of given invocation with the expected constraints
     *
     * If a constraint is not an instance of PHPUnit_Framework_Constraint it
     * will automatically use PHPUnit_Framework_Constraint_IsEqual.
     *
     * @param   int                                                                       $invocation  number of invocation to check
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit_Framework_Constraint[]  $expected    constraints which describe expected parameters
     * @return  bool
     * @throws  \bovigo\callmap\ArgumentMismatch
     */
    private function verifyArgs($invocation, array $expected)
    {
        $received = $this->callmap->argumentsReceivedFor($this->method, $invocation);
        if (count($received['arguments']) < count($expected)) {
            throw new ArgumentMismatch(sprintf(
                    'Argument count for invocation #%d of %s is too'
                    . ' low: received %d argument(s), expected %d argument(s).',
                    $invocation,
                    methodName($this->callmap, $this->method),
                    count($received['arguments']),
                    count($expected)
            ));
        }

        foreach ($expected as $index => $constraint) {
            $this->evaluate(
                    $constraint,
                    isset($received['arguments'][$index]) ? $received['arguments'][$index] : null,
                    sprintf(
                            'Parameter %sat position %d for invocation #%d of %s'
                            . ' does not match expected value.',
                            isset($received['names'][$index]) ? '$' . $received['names'][$index] . ' ' : '',
                            $index,
                            $invocation,
                            methodName($this->callmap, $this->method),
                            $this->method
                    )
            );
        }

        return true;
    }

    /**
     * evaluates given constraint given received argument
     *
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit_Framework_Constraint  $constraint  constraint for argument
     * @param   mixed                                                                   $received    actually received argument
     * @param   string                                                                  $description  description for invocation in case of error
     * @return  bool
     * @throws  \RuntimeException  in case neither bovigo/assert, PHPUnit not xp-framework/core is present
     */
    private function evaluate($constraint, $received, $description)
    {
        if (function_exists('bovigo\assert\assert')) {
            return \bovigo\assert\assert(
                    $received,
                    $this->predicateFor($constraint),
                    $description
            );
        }

        if ($constraint instanceof \PHPUnit_Framework_Constraint) {
            return $constraint->evaluate($received, $description);
        }

        if (class_exists('\PHPUnit_Framework_Constraint_IsEqual')) {
            return $this->evaluate(
                    new \PHPUnit_Framework_Constraint_IsEqual($constraint),
                    $received,
                    $description
            );
        }

        if (class_exists('\unittest\TestCase')) {
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

        throw new \RuntimeException('Neither bovigo/assert, PHPUnit nor xp-framework/core found, can not perform argument verification');
    }

    /**
     * creates precicate for given constraint
     *
     * @param   mixed|\bovigo\assert\predicate\Predicate|\PHPUnit_Framework_Constraint  $constraint
     * @return  \bovigo\assert\predicate\Predicate
     */
    private function predicateFor($constraint)
    {
        if ($constraint instanceof \PHPUnit_Framework_Constraint) {
            return new \bovigo\assert\phpunit\ConstraintAdapter($constraint);
        }

        if ($constraint instanceof \bovigo\assert\predicate\Predicate) {
            return $constraint;
        }

        return \bovigo\assert\predicate\equals($constraint);
    }
}
