<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap\internal;

use bovigo\callmap\ClassProxy;
use InvalidArgumentException;
use ReflectionMethod;
/**
 * CallMapProxy to be mixed into class proxies generated by NewInstance.
 *
 * @internal
 */
trait ClassProxyMethodHandler
{
    /**
     * map of method with closures to call instead
     *
     * @type  CallMap
     */
    private $callMap;
    /**
     * map of invocations for methods
     */
    private array $invocations = [];
    /**
     * switch whether passing calls to parent class is allowed
     */
    private bool $parentCallsAllowed = true;
    /**
     * @type  array<string,int>
     */
    private array $stubs = [];

    /**
     * disable passing calls to parent class
     *
     * @internal
     */
    public function preventParentCalls(): void
    {
        $this->parentCallsAllowed = false;
    }

    /**
     * sets the call map with return values
     *
     * @api
     * @since  3.2.0
     * @param  array<string,mixed> $callMap
     * @return ClassProxy
     * @throws InvalidArgumentException in case any of the mapped methods does not exist or is not applicable
     */
    public function returns(array $callMap): ClassProxy
    {
        foreach (array_keys($callMap) as $method) {
            if (!isset($this->_allowedMethods[$method]) || isset($this->_voidMethods[$method])) {
                throw new InvalidArgumentException($this->canNot('map', $method));
            }
        }

        $this->callMap = new CallMap($callMap);
        return $this;
    }

    /**
     * ensures given methods are stubbed and will not call parent method
     *
     * @api
     * @since  5.1.0
     * @throws InvalidArgumentException in case any of the mapped methods does not exist or is not applicable
     */
    public function stub(string ...$methods): ClassProxy
    {
        foreach ($methods as $method) {
            if (!isset($this->_allowedMethods[$method])) {
                throw new InvalidArgumentException($this->canNot('stub', $method));
            }

            if (null !== $this->callMap && $this->callMap->hasResult($method)) {
                throw new InvalidArgumentException(sprintf(
                    'Trying to stub method %s, but it was already mapped with a return value.',
                     $this->completeNameOf($method)
                ));
            }
        }

        $this->stubs = array_flip($methods);
        return $this;
    }

    /**
     * handles actual method calls
     */
    protected function handleMethodCall(
        string $method,
        array $arguments,
        bool $shouldReturnSelf
    ): mixed {
        $invocation = $this->invocations($method)->recordCall($arguments);
        if (
            null !== $this->callMap
            && $this->callMap->hasResultFor($method, $invocation)
        ) {
            return $this->callMap->resultFor($method, $arguments, $invocation);
        }

        if (
            $this->parentCallsAllowed
            && !isset($this->stubs[$method])
            && get_parent_class($this) !== false
        ) {
            // is_callable() returns true even for abstract methods
            $refMethod = new ReflectionMethod(get_parent_class($this), $method);
            if (!$refMethod->isAbstract()) {
                return parent::$method(...$arguments);
            }
        }

        return $shouldReturnSelf ? $this : null;
    }

    /**
     * returns recorded invocations for given method
     *
     * @throws InvalidArgumentException in case the method does not exist or is not applicable
     * @since  3.1.0
     */
    public function invocations(string $method): Invocations
    {
        if (empty($method)) {
            throw new InvalidArgumentException(
                'Please provide a method name to retrieve invocations for.'
            );
        }

        if (!isset($this->_allowedMethods[$method])) {
            throw new InvalidArgumentException(
                $this->canNot('retrieve invocations for', $method)
            );
        }

        if (!isset($this->invocations[$method])) {
            $this->invocations[$method] = new Invocations(
                $this->completeNameOf($method),
                $this->_methodParams[$method]
            );
        }

        return $this->invocations[$method];
    }

    /**
     * returns complete name of the proxied class/interface/trait method
     */
    private function completeNameOf(string $method): string
    {
        return str_replace(
            ['CallMapProxy', 'CallMapFork'],
            '',
            get_class($this)
        ) . '::' . $method . '()';
    }

    /**
     * creates complete error message that invalid method can not be used
     */
    private function canNot(string $message, string $invalidMethod): string
    {
        if (isset($this->_voidMethods[$invalidMethod])) {
            $reason = 'is declared as returning void or never.';
        } elseif (method_exists($this, $invalidMethod)) {
            $reason = 'is not applicable for ' . ('stub' === $message ? 'stubbing' : 'mapping') . '.';
        } else {
            $reason = 'does not exist. Probably a typo?';
        }

        return sprintf(
            'Trying to %s method %s, but it %s',
            $message,
            $this->completeNameOf($invalidMethod),
            $reason
        );
    }
}
