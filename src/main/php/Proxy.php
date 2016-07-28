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
     * returns recorded invocations for given method
     *
     * @internal  use verify($proxy, $method)->*() instead
     * @param   string  $method
     * @return  Invocations
     * @throws  \InvalidArgumentException  in case the method does not exist or is not applicable
     * @since   3.1.0
     */
    public function invocations(string $method): Invocations;
}
