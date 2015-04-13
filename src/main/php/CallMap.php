<?php
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
     * @param   int     $invocationCound  denotes which nth invocation of the method this is
     * @return  bool
     */
    public function hasResultFor($method, $invocationCound)
    {
        if (!isset($this->callMap[$method])) {
            return false;
        }

   #     if ($this->callMap[$method] instanceof InvocationResults) {
   #         return $this->callMap[$method]->hasResultForInvocation($invocationCound - 1);
   #     }

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
    public function resultFor($method, $arguments, $invocationCount)
    {
        if (isset($this->callMap[$method])) {
            if (is_callable($this->callMap[$method])) {
                return call_user_func_array($this->callMap[$method], $arguments);
            } elseif ($this->callMap[$method] instanceof InvocationResults) {
                return $this->callMap[$method]->valueForInvocation($invocationCount - 1);
            } elseif ($this->callMap[$method] instanceof InvocationThrow) {
                throw $this->callMap[$method]->exception();
            }

            return $this->callMap[$method];
        }

        return null;
    }
}

