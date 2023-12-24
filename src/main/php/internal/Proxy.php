<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap\internal;

/**
 * A proxy records all method/function calls.
 */
interface Proxy
{
    /**
     * returns recorded invocations for given method
     *
     * @internal use verify($proxy, $method)->*() instead
     * @throws \InvalidArgumentException in case the method does not exist or is not applicable
     * @since  3.1.0
     */
    public function invocations(string $method): Invocations;
}
