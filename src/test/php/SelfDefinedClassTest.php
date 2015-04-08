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
 * Helper class for the test.
 */
class SelfDefined
{
    public function action(self $self, callable $something, array $optional = [], $roland = 313)
    {
        return 'selfdefined';
    }
}
/**
 * Applies tests to a self defined class.
 */
class SelfDefinedClassTest extends \PHPUnit_Framework_TestCase
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
        $this->proxy = NewInstance::of(new SelfDefined());
    }

    /**
     * @test
     */
    public function callsOriginalMethodIfNoMappingProvided()
    {
        assertEquals(
                'selfdefined',
                $this->proxy->action(new SelfDefined(), function() {})
        );
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->mapCalls(['action' => 'foo']);
        assertEquals(
                'foo',
                $this->proxy->action(new SelfDefined(), function() {})
        );
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['action' => function() { return 'foo'; }]);
        assertEquals(
                'foo',
                $this->proxy->action(new SelfDefined(), function() {})
        );
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        assertEquals(0, $this->proxy->callsReceivedFor('action'));
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->action(new SelfDefined(), function() {});
        $this->proxy->action(new SelfDefined(), function() {});
        assertEquals(2, $this->proxy->callsReceivedFor('action'));
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullIfMethodNotCalled()
    {
        assertNull($this->proxy->argumentsReceived('action'));
    }

    /**
     * @test
     */
    public function returnsListOfReceivedArgumentsIfMethodCalled()
    {
        $arg1 = new SelfDefined();
        $arg2 = function() {};
        $this->proxy->action($arg1, $arg2);
        assertEquals(
                [$arg1, $arg2],
                $this->proxy->argumentsReceived('action')
        );
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullWhenNotCalledForRequestedInvocationCount()
    {
        $this->proxy->action(new SelfDefined(), function() {});
        assertNull($this->proxy->argumentsReceived('action', 2));
    }
}
