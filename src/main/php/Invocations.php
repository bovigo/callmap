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
 * Collects information about invocations of a function or method.
 *
 * @since  3.1.0
 * @internal
 */
class Invocations implements \Countable
{
    /**
     * @var array<array<mixed>>
     */
    private array $callHistory = [];

    /**
     * constructor
     *
     * @param string   $name       name of method or function the call history is recorded for
     * @param string[] $paramNames list of parameter names
     */
    public function __construct(private string $name, private array $paramNames) { }

    /**
     * returns name of method or function these invocations are from
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * records method/function call
     *
     * @param  mixed[] $arguments list of passed arguments
     * @return int amount of calls for given method
     */
    public function recordCall(array $arguments): int
    {
        $this->callHistory[] = $arguments;
        return count($this->callHistory);
    }

    /**
     * returns amount of calls received
     */
    public function count(): int
    {
        return count($this->callHistory);
    }

    /**
     * returns name of argument at requested position
     *
     * Returns null if there is no argument at requested position or the name
     * of that argument is unknown.
     *
     * @param  int    $argumentPosition
     * @param  string $suffix optional string to append after argument name
     * @return string|null
     */
    public function argumentName(int $argumentPosition, string $suffix = ''): ?string
    {
        if  (isset($this->paramNames[$argumentPosition])) {
            return '$' . $this->paramNames[$argumentPosition] . $suffix;
        }

        return null;
    }

    /**
     * returns the arguments received for a specific invocation
     *
     * @throws MissingInvocation in case no such invocation was received
     */
    public function argumentsOf(int $invocation = 1): array
    {
        if (isset($this->callHistory[$invocation - 1])) {
            return $this->callHistory[$invocation - 1];
        }

        $totalInvocations = $this->count();
        throw new MissingInvocation(sprintf(
            'Missing invocation #%d for %s, was %s.',
            $invocation,
            $this->name,
            ($totalInvocations === 0 ?
                'never called' :
                ('only called ' . ($totalInvocations === 1 ?
                    'once' : $totalInvocations . ' times')
                )
            )
        ));
    }
}
