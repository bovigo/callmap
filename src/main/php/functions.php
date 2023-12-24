<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap {
    use bovigo\callmap\verification\Verification;
    use Closure;
    use Throwable;

    /**
     * creates a closure which throws the given exception when invoked
     *
     * @api
     * @since 0.2.0
     */
    function throws(Throwable $e): Closure
    {
        return function() use ($e) { throw $e; };
    }

    /**
     * wraps given callable into a closure so that the given callable is returned and not executed
     *
     * @api
     * @since 0.6.0
     */
    function wrap(callable $callable): Closure
    {
        return function() use ($callable) { return $callable; };
    }

    /**
     * creates a list of invocation results
     *
     * @api
     * @since 0.2.0
     */
    function onConsecutiveCalls(mixed ...$values): InvocationResults
    {
        return new InvocationResults($values);
    }

    /**
     * returns possibilities to verify method or function invocations
     *
     * Parameter $method can be left away when the proxy to be verified is a
     * function proxy. For a class proxy the parameter is required.
     *
     * @api
     * @since 0.5.0
     */
    function verify(Proxy $proxy, string $method = ''): Verification
    {
        return new Verification($proxy->invocations($method));
    }

    /**
     * internal helper function to be able to mock eval in tests
     *
     * Since eval() is a language construct and not a function but we want to
     * mock it when testing dynamic code creation we wrap it into our own
     * function.
     *
     * @since 3.0.0
     * @internal
     */
    function compile(string $code)
    {
        return eval($code);
    }

    /**
     * exclude some of our own classes from being displayed in PHPUnit error stacks
     */
    if (class_exists(\PHPUnit\Util\ExcludeList::class)) {
        \PHPUnit\Util\ExcludeList::addDirectory(__DIR__ . DIRECTORY_SEPARATOR);
    }
}
