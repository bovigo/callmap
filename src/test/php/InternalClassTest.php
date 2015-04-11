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
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        assertEquals(0, $this->proxy->callsReceivedFor('getNamespaceName'));
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->getName();
        $this->proxy->getName();
        $this->proxy->getShortName();
        assertEquals(2, $this->proxy->callsReceivedFor('getName'));
        assertEquals(1, $this->proxy->callsReceivedFor('getShortName'));
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullIfMethodNotCalled()
    {
        assertNull($this->proxy->argumentsReceivedFor('implementsInterface'));
    }

    /**
     * @test
     */
    public function returnsListOfReceivedArgumentsIfMethodCalled()
    {
        $this->proxy->implementsInterface('bovigo\callmap\Proxy');
        assertEquals(
                ['bovigo\callmap\Proxy'],
                $this->proxy->argumentsReceivedFor('implementsInterface')
        );
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullWhenNotCalledForRequestedInvocationCount()
    {
        $this->proxy->implementsInterface('bovigo\callmap\Proxy');
        assertNull($this->proxy->argumentsReceivedFor('implementsInterface', 2));
    }
}
