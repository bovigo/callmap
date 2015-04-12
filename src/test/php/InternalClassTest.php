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
 * Applies tests to a PHP internal class.
 */
class InternalClassTest extends \PHPUnit_Framework_TestCase
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
        $this->proxy = NewInstance::of('\ReflectionObject', [$this]);
    }

    /**
     * @test
     */
    public function callsOriginalMethodIfNoMappingProvided()
    {
        assertEquals(__CLASS__, $this->proxy->getName());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->mapCalls(['getName' => 'foo']);
        assertEquals('foo', $this->proxy->getName());
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['getName' => function() { return 'foo'; }]);
        assertEquals('foo', $this->proxy->getName());
    }

    /**
     * @test
     * @since  0.4.0
     */
    public function mapToCallableReturnsCallableReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['getName' => 'strtoupper']);
        assertEquals('FOO', $this->proxy->getName('foo'));
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        verify($this->proxy, 'getNamespaceName')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->getName();
        $this->proxy->getName();
        $this->proxy->getShortName();
        verify($this->proxy,'getName')->wasCalled(2);
        verify($this->proxy, 'getShortName')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function canVerifyReceivedArguments()
    {
        $this->proxy->implementsInterface('bovigo\callmap\Proxy');
        verify($this->proxy, 'implementsInterface')->received('bovigo\callmap\Proxy');
    }

    /**
     * @test
     */
    public function canVerifyReceivedArgumentsOfSpecificInvocation()
    {
        $this->proxy->hasProperty('foo');
        $this->proxy->hasProperty('bar');
        verify($this->proxy, 'hasProperty')->receivedOn(2, 'bar');
    }
}
