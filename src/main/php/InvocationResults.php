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
     * checks whether a result for the invocation with the given number exists
     *
     * @param   int  $number
     * @return  bool
     */
    public function hasResultForInvocation(int $number): bool
    {
        return isset($this->results[$number]);
    }

    /**
     * returns the result for invocation with the given number
     *
     * @param   int  $number
     * @return  mixed
     */
    public function resultForInvocation(int $number)
    {
        return $this->results[$number] ?? null;
    }
}
