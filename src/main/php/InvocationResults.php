<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap;
/**
 * Represents a list of invocation results.
 *
 * @api
 */
class InvocationResults
{
    /**
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
     */
    public function valueForInvocation($number)
    {
        if (isset($this->results[$number])) {
            if (is_callable($this->results[$number])) {
                return $this->results[$number]();
            }

            return $this->results[$number];
        }

        return null;
    }
}
