<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
/**
 * Applies tests to a PHP internal interface.
 */
class InternalInterfaceTest extends TestCase
{
    /**
     * @type  bovigo\callmap\Proxy
     */
    private $proxy;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->proxy = NewInstance::of(\Countable::class);
    }

    /**
     * @test
     */
    public function returnsNullIfMethodCallNotMapped()
    {
        assert($this->proxy->count(), isNull());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->mapCalls(['count' => 3]);
        assert($this->proxy->count(), equals(3));
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['count' => function() { return 42; }]);
        assert($this->proxy->count(), equals(42));
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        verify($this->proxy, 'count')->wasNeverCalled();
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->count();
        $this->proxy->count();
        verify($this->proxy, 'count')->wasCalled(2);
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function canVerifyThatMethodDidNotReveiveArguments()
    {
        $this->proxy->count();
        verify($this->proxy, 'count')->receivedNothing();
    }
}
