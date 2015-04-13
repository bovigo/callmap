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
 * wraps given callable into another callable so that the given callable is returned and not executed
 *
 * @api
 * @param   callable  $callable
 * @return  callable
 * @since   0.6.0
 */
function wrap(callable $callable)
{
    return function() use ($callable) { return $callable; };
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
 * returns possibilities to verify method invocations on the callmap
 *
 * @api
 * @param   \bovigo\callmap\Proxy  $callmap  callmap to verify
 * @param   string                 $method   actual method to verify
 * @return  \bovigo\callmap\Verify
 * @since   0.5.0
 */
function verify(Proxy $callmap, $method)
{
    return new Verification($callmap, $method);
}

/**
 * returns name of the proxied class/interface/trait
 *
 * @internal
 * @param   \bovigo\callmap\Proxy  $callmap  callmap to return method name for
 * @param   string                 $method   actual method to return
 * @return  string
 */
function methodName(Proxy $callmap, $method)
{
    return str_replace(
            ['CallMapProxy', 'CallMapFork'],
            '',
            get_class($callmap)
    ) . '::' . $method . '()';
}
