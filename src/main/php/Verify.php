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
class Verify
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
                            . ' but actually never called.',
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
        if  (count($received) === 0) {
            return true;
        }

        throw new ArgumentMismatch(
                sprintf(
                    'Argument count for invocation #%d of %s is too'
                    . ' high: received %d argument(s), expected no arguments.',
                    $invocation,
                    methodName($this->callmap, $this->method),
                    count($received)
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
     * @param   mixed|\PHPUnit_Framework_Constraint[]  ...$expected  constraints which describe expected parameters
     * @return  bool
     */
    public function received()
    {
        return $this->verifyArgs(1, func_get_args());
    }

    /**
     * verifies that the received arguments match expected arguments for the given invocation
     *
     * If a constraint is not an instance of PHPUnit_Framework_Constraint it
     * will automatically use PHPUnit_Framework_Constraint_IsEqual.
     *
     * @api
     * @param   int                                    $invocation   nth invocation to check
     * @param   mixed|\PHPUnit_Framework_Constraint[]  ...$expected  constraints which describe expected parameters
     * @return  bool
     */
    public function receivedOn()
    {
        $args = func_get_args();
        $invocation = array_shift($args);
        return $this->verifyArgs($invocation, $args);
    }

    /**
     * verifies arguments of given invocation with the expected constraints
     *
     * If a constraint is not an instance of PHPUnit_Framework_Constraint it
     * will automatically use PHPUnit_Framework_Constraint_IsEqual.
     *
     * @param   int                                    $invocation  number of invocation to check
     * @param   mixed|\PHPUnit_Framework_Constraint[]  $expected    constraints which describe expected parameters
     * @return  bool
     * @throws  \bovigo\callmap\ArgumentMismatch
     */
    private function verifyArgs($invocation, array $expected)
    {
        $received = $this->callmap->argumentsReceivedFor($this->method, $invocation);
        if (count($received) < count($expected)) {
            throw new ArgumentMismatch(sprintf(
                    'Argument count for invocation #%d of %s is too'
                    . ' low: received %d argument(s), expected %d argument(s).',
                    $invocation,
                    methodName($this->callmap, $this->method),
                    count($received),
                    count($expected)
            ));
        }

        foreach ($expected as $key => $constraint) {
            $this->evaluate(
                    $constraint,
                    $key,
                    isset($received[$key]) ? $received[$key] : null
            );
        }

        return true;
    }

    /**
     * evaluates given constraint given received argument
     *
     * @param   mixed|\PHPUnit_Framework_Constraint  $constraint  constraint for argument
     * @param   int                                  $key         position of argument
     * @param   mixed                                $received    actually received argument
     */
    private function evaluate($constraint, $key, $received)
    {
        if ($constraint instanceof \PHPUnit_Framework_Constraint) {
            return $constraint->evaluate(
                    $received,
                    sprintf(
                            'Parameter %s for invocation #%s of %s'
                            . ' does not match expected value',
                            $key,
                            1,
                            methodName($this->callmap, $this->method),
                            $this->method
                    )
            );
        }

        return $this->evaluate(
                new \PHPUnit_Framework_Constraint_IsEqual($constraint),
                $key,
                $received
        );
    }
}
