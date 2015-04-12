<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap;
/**
 * Applies tests to a PHP internal interface.
 */
class InternalInterfaceTest extends \PHPUnit_Framework_TestCase
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
        $this->proxy = NewInstance::of('\Countable');
    }

    /**
     * @test
     */
    public function returnsNullIfMethodCallNotMapped()
    {
        assertNull($this->proxy->count());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->mapCalls(['count' => 3]);
        assertEquals(3, $this->proxy->count());
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['count' => function() { return 42; }]);
        assertEquals(42, $this->proxy->count());
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        assertTrue(verify($this->proxy, 'count')->wasNeverCalled());
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->count();
        $this->proxy->count();
        assertTrue(verify($this->proxy, 'count')->wasCalled(2));
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullIfMethodNotCalled()
    {
        assertNull($this->proxy->argumentsReceivedFor('count'));
    }

    /**
     * @test
     */
    public function returnsListOfReceivedArgumentsIfMethodCalled()
    {
        $this->proxy->count('bovigo\callmap\Proxy');
        assertEquals(
                ['bovigo\callmap\Proxy'],
                $this->proxy->argumentsReceivedFor('count')
        );
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullWhenNotCalledForRequestedInvocationCount()
    {
        $this->proxy->count('bovigo\callmap\Proxy');
        assertNull($this->proxy->argumentsReceivedFor('count', 2));
    }
}
