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
 * Represents the actual call map.
 *
 * @internal
 */
class CallMap
{
    /**
     * map of method with results for their invocation
     *
     * @type  array
     */
    private $callMap = [];

    /**
     * constructor
     *
     * @param  array  $callMap  map of method with results for their invocation
     */
    public function __construct(array $callMap)
    {
        $this->callMap = $callMap;
    }

    /**
     * checks whether callmap has a result for the invocation of method
     *
     * @param   string  $method           name of invoked method
     * @param   int     $invocationCount  denotes which nth invocation of the method this is
     * @return  bool
     */
    public function hasResultFor(string $method, int $invocationCount): bool
    {
        if (!array_key_exists($method, $this->callMap)) {
            return false;
        }

        if ($this->callMap[$method] instanceof InvocationResults) {
            return $this->callMap[$method]->hasResultForInvocation($invocationCount - 1);
        }

        return true;
    }

    /**
     * returns the result for the method invocation done with given arguments
     *
     * @param   string   $method           name of invoked method
     * @param   mixed[]  $arguments        arguments passed for the method call
     * @param   int      $invocationCount  denotes which nth invocation of the method this is
     * @return  mixed
     * @throws  \Exception
     */
    public function resultFor(string $method, array $arguments, int $invocationCount)
    {
        if (isset($this->callMap[$method])) {
            if ($this->callMap[$method] instanceof InvocationResults) {
                $result = $this->callMap[$method]->resultForInvocation($invocationCount - 1);
            } else {
                $result = $this->callMap[$method];
            }

            if (is_callable($result)) {
                return call_user_func_array($result, $arguments);
            } elseif ($result instanceof InvocationThrow) {
                throw $result->exception();
            }

            return $result;
        }

        return null;
    }
}
