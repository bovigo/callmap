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
 * Proxy to extend from when creating new function proxies via NewCallable.
 *
 * @since  3.1.0
 */
abstract class FunctionProxy implements Proxy
{
    /**
     * map of method with closures to call instead
     *
     * @type  \bovigo\callmap\CallMap
     */
    private $callMap;
    /**
     * @type  \bovigo\callmap\Invocations
     */
    private $invocations;
    /**
     * @type  bool
     */
    protected $parentCallsAllowed = true;

    /**
     * constructor
     *
     * @param  string  $functionName  name of proxied function
     */
    public function __construct(string $functionName)
    {
        $this->invocations = new Invocations($functionName, $this->paramNames);
    }

    /**
     * sets the call map to use
     *
     * @api
     * @param   mixed  $returnValue
     * @return  $this
     * @since   3.2.0
     */
    public function returns($returnValue): self
    {
        $this->callMap = new CallMap(['function' => $returnValue]);
        return $this;
    }

    /**
     * sets the call map to use
     *
     * @deprecated  since 3.2.0, use returns() instead, will likely be removed with 4.0.0
     * @param   mixed  $returnValue
     * @return  $this
     */
    public function mapCall($returnValue): self
    {
        return $this->returns($returnValue);
    }

    /**
     * shortcut for returns(throws($e))
     *
     * @api
     * @param   \Throwable  $e
     * @return  $this
     * @since   3.2.0
     */
    public function throws(\Throwable $e): self
    {
        $this->callMap = new CallMap(['function' => throws($e)]);
        return $this;
    }

    /**
     * handles actual method calls
     *
     * @param   mixed[]   $arguments  list of given arguments for methods
     * @return  mixed
     */
    protected function handleFunctionCall(array $arguments)
    {
        $invocation = $this->invocations->recordCall($arguments);
        if (null !== $this->callMap && $this->callMap->hasResultFor('function', $invocation)) {
            return $this->callMap->resultFor('function', $arguments, $invocation);
        }

        if ($this->parentCallsAllowed) {
            $originalFunction = $this->invocations->name();
            return $originalFunction(...$arguments);
        }

        return null;
    }

    /**
     * returns recorded invocations for function
     *
     * @internal  use verify($proxy)->*() instead
     * @param   string  $method  param is ignored
     * @return  Invocations
     */
    public function invocations(string $method): Invocations
    {
        return $this->invocations;
    }
}
