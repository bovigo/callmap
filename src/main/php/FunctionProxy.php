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
    public function __construct($functionName)
    {
        $this->invocations = new Invocations($functionName, $this->paramNames);
    }

    /**
     * sets the call map to use
     *
     * @param   mixed  $returnValue
     * @return  $this
     */
    public function mapCall($returnValue): self
    {
        $this->callMap = new CallMap(['function' => $returnValue]);
        return $this;
    }

    /**
     * handles actual method calls
     *
     * @param   mixed[]   $arguments  list of given arguments for methods
     * @return  mixed
     * @throws  \Exception
     */
    protected function handleFunctionCall(array $arguments)
    {
        $invocation = $this->invocations->recordCall($arguments);
        if (null !== $this->callMap && $this->callMap->hasResultFor('function', $invocation)) {
            return $this->callMap->resultFor('function', $arguments, $invocation);
        }

        if ($this->parentCallsAllowed) {
            return call_user_func_array($this->invocations->name(), $arguments);
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
