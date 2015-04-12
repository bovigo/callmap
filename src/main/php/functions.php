<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
/**
 * creates an invocation result which throws the given exception
 *
 * @api
 * @param   \Exception  $e
 * @return  \bovigo\callmap\InvocationThrow
 * @since   0.2.0
 */
function throws(\Exception $e)
{
    return new InvocationThrow($e);
}

/**
 * creates a list of invocation results
 *
 * @api
 * @param   mixed  ...$value
 * @return  \bovigo\callmap\InvocationResults
 * @since   0.2.0
 */
function onConsecutiveCalls()
{
    return new InvocationResults(func_get_args());
}

/**
 * returns possibilities to verify the amount of calls for method on callmap
 *
 * @param   \bovigo\callmap\Proxy  $callmap  callmap to verify method call amount of
 * @param   string                 $method   actual method to verify
 * @return  \bovigo\callmap\Verify
 * @since   0.5.0
 */
function verify(Proxy $callmap, $method)
{
    return new Verify($callmap, $method);
}
