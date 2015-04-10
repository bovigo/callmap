<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
/**
 * Represents a list of invocation results.
 *
 * @internal  Do not use directly, call bovigo\callmap\onConsecutiveCalls() instead.
 */
class InvocationResults
{
    /**
     * list of invocation results
     *
     * @type  mixed[]
     */
    private $results;

    /**
     * constructor
     *
     * @param  mixed[]  $results  list of invocation results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * returns the result for invocation with the given number
     *
     * @param   int  $number
     * @return  mixed
     * @throws  \Exception
     */
    public function valueForInvocation($number)
    {
        if (isset($this->results[$number])) {
            if ($this->results[$number] instanceof InvocationThrow) {
                throw $this->results[$number]->exception();
            }

            return $this->results[$number];
        }

        return null;
    }
}
