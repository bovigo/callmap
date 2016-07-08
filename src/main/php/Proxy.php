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
 * A callmap proxy can stub method calls and record all method calls.
 */
interface Proxy
{
    /**
     * sets the call map to use
     *
     * @api
     * @param   array  $callMap
     * @return  $this
     * @throws  \InvalidArgumentException  in case any of the mapped methods does not exist or is not applicable
     */
    public function mapCalls(array $callMap): self;

    /**
     * returns amount of calls received for given method
     *
     * @internal  use verify()->was*() instead
     * @param   string  $method  name of method to check
     * @return  int
     * @throws  \InvalidArgumentException  in case the method does not exist or is not applicable
     */
    public function callsReceivedFor(string $method): int;

    /**
     * returns the arguments received for a specific call
     *
     * @internal  use verify()->received*() instead
     * @param   string  $method      name of method to check
     * @param   int     $invocation  nth invocation to check
     * @return  mixed[]
     * @throws  \InvalidArgumentException  in case the method does not exist or is not applicable
     * @throws  \bovigo\callmap\MissingInvocation  in case no such invocation was received
     */
    public function argumentsReceivedFor(string $method, int $invocation = 0): array;
}
