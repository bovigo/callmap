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
     * returns name of the proxied class/interface/trait
     *
     * @return  string
     */
    private function callmapClass()
    {
        return str_replace(
                ['CallMapProxy', 'CallMapFork'],
                '',
                get_class($this->callmap)
        );
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
                    $this->callmapClass() . '::' . $this->method . '() '
                    . 'was expected to be called at most ' . $times
                    . ' time(s), but actually called '
                    . $this->callmap->callsReceivedFor($this->method) . ' time(s).'
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
                    $this->callmapClass() . '::' . $this->method . '() '
                    . 'was expected to be called at least once, '
                    . 'but actually never called.'
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
                    $this->callmapClass() . '::' . $this->method . '() '
                    . 'was expected to be called at least ' . $times
                    . ' time(s), but actually called '
                    . $this->callmap->callsReceivedFor($this->method) . ' time(s).'
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
        if ($this->callmap->callsReceivedFor($this->method) !== 1) {
            if ($this->callmap->callsReceivedFor($this->method) > 1) {
                $amount = 'called '
                    . $this->callmap->callsReceivedFor($this->method) . ' time(s).';
            } else {
                $amount = 'never called.';
            }

            throw new CallAmountViolation(
                    $this->callmapClass() . '::' . $this->method . '() '
                    . 'was expected to be called once, but actually '
                    . $amount
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
                    $this->callmapClass() . '::' . $this->method . '() '
                    . 'was expected to be called ' . $times . ' times, but actually called '
                    . $this->callmap->callsReceivedFor($this->method) . ' time(s).'
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
                    $this->callmapClass() . '::' . $this->method . '() '
                    . 'was not expected to be called, but actually called '
                    . $this->callmap->callsReceivedFor($this->method) . ' time(s).'
            );
        }

        return true;
    }
}
