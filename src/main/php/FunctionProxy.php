<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;

use bovigo\callmap\internal\CallMap;
use bovigo\callmap\internal\Invocations;
use bovigo\callmap\internal\Proxy;
use InvalidArgumentException;
use LogicException;
use Throwable;
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
     * @var callable
     */
    private $name;
    /**
     * map of method with closures to call instead
     *
     * @var CallMap
     */
    private $callMap;
    private Invocations $invocations;
    protected bool $parentCallsAllowed = true;
    /**
     * overwritten by proxy generated from NewCallable
     */
    protected array $paramNames = [];
    /**
     * overwritten by proxy generated from NewCallable
     */
    protected bool $returnVoid = true;

    /**
     * @param  callable  $functionName  name of proxied function
     * @throws InvalidArgumentException
     */
    public function __construct(callable $functionName)
    {
        if (!\is_string($functionName)) {
            throw new InvalidArgumentException(
                'Given function name must be a function name, methods are not supported'
            );
        }

        $this->name = $functionName;
        $this->invocations = new Invocations($functionName, $this->paramNames);
    }

    /**
     * turns this into a stub which doesn't forward method calls to the original function
     *
     * @internal
     */
    public function preventParentCalls(): void
    {
        $this->parentCallsAllowed = false;
    }

    /**
     * sets the call map to use
     *
     * @api
     * @throws LogicException when mapped function is declared as returning void
     * @since  3.2.0
     */
    public function returns(mixed $returnValue): self
    {
        if ($this->returnVoid) {
            throw new LogicException(
                // should be $this->name, but phpstan doesn't understand that
                // it is always a string even when marked as type callable
                'Trying to map function ' . $this->invocations->name()
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
     * @since 3.2.0
     */
    public function throws(Throwable $e): self
    {
        $this->callMap = new CallMap(['function' => throws($e)]);
        return $this;
    }

    /**
     * handles actual function calls
     *
     * @param  mixed[] $arguments list of given arguments for function
     */
    protected function handleFunctionCall(array $arguments): mixed
    {
        $invocation = $this->invocations->recordCall($arguments);
        if (
            null !== $this->callMap
            && $this->callMap->hasResultFor('function', $invocation)
        ) {
            return $this->callMap->resultFor('function', $arguments, $invocation);
        }

        if ($this->parentCallsAllowed) {
            $originalFunction = $this->name;
            return $originalFunction(...$arguments);
        }

        return null;
    }

    /**
     * returns recorded invocations for function
     *
     * @internal  use verify($proxy)->*() instead
     */
    public function invocations(string $method): Invocations
    {
        return $this->invocations;
    }
}
