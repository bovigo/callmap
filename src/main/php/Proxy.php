<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
/**
 * A callmap proxy can stub method calls and record all method calls.
 *
 * @api
 */
interface Proxy
{
    /**
     * sets the call map to use
     *
     * @param   array  $callMap
     * @return  $this
     */
    public function mapCalls(array $callMap);

    /**
     * returns amount of calls received for given method
     *
     * @param   string  $method  name of method to check
     * @return  int
     */
    public function callsReceivedFor($method);

    /**
     * returns the arguments received for a specific call
     *
     * @param   string  $method      name of method to check
     * @param   int     $invocation  nth invocation to check
     * @return  mixed[]
     */
    public function argumentsReceived($method, $invocation = 0);
}
