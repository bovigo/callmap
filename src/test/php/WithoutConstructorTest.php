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
class ClassWithConstructor
{
    private $foo;

    public function __construct(\stdClass $foo)
    {
        $this->foo = $foo;
    }

    public function action()
    {
        return $this->foo->bar;
    }
}
/**
 * Applies tests to a stub of a class.
 */
class WithoutConstructorTest extends \PHPUnit_Framework_TestCase
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
        $this->proxy = NewInstance::stub('bovigo\callmap\ClassWithConstructor');
    }
    /**
     * @test
     */
    public function returnsNullIfMethodCallNotMapped()
    {
        assertNull($this->proxy->action());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->mapCalls(['action' => 3]);
        assertEquals(3, $this->proxy->action());
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['action' => function() { return 42; }]);
        assertEquals(42, $this->proxy->action());
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        verify($this->proxy, 'action')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->action();
        $this->proxy->action();
        verify($this->proxy, 'action')->wasCalled(2);
    }

    /**
     * @test
     */
    public function returnsListOfReceivedArgumentsIfMethodCalled()
    {
        $this->proxy->action(303);
        verify($this->proxy, 'action')->received(303);
    }
}
