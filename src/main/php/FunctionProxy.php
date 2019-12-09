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
     * name of mocked function
     *
     * @var  string
     */
    private $name;
    /**
     * map of method with closures to call instead
     *
     * @var  \bovigo\callmap\CallMap
     */
    private $callMap;
    /**
     * @var  \bovigo\callmap\Invocations
     */
    private $invocations;
    /**
     * @var  bool
     */
    protected $parentCallsAllowed = true;
    /**
     * overwritten by proxy generated from NewCallable
     *
     * @var  array
     */
    protected $paramNames = [];
    /**
     * overwritten by proxy generated from NewCallable
     *
     * @var  bool
     */
    protected $returnVoid;

    /**
     * constructor
     *
     * @param  string  $functionName  name of proxied function
     */
    public function __construct(string $functionName)
    {
        $this->name = $functionName;
        $this->invocations = new Invocations($functionName, $this->paramNames);
    }

    /**
     * sets the call map to use
     *
     * @api
     * @param   mixed  $returnValue
     * @return  $this
     * @throws  \LogicException when mapped function is declared as returning void
     * @since   3.2.0
     */
    public function returns($returnValue): self
    {
        if ($this->returnVoid) {
            throw new \LogicException(
                'Trying to map function ' . $this->name
                . '(), but it is declared as returning void.'
            );
        }

        $this->callMap = new CallMap(['function' => $returnValue]);
        return $this;
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
     * handles actual function calls
     *
     * @param   mixed[]  $arguments  list of given arguments for function
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
