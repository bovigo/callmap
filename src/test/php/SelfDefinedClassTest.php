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

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Helper class for the test.
 */
class SelfDefined
{
    public function action(self $self, callable $something, array $optional = [], $roland = 303): string
    {
        return 'selfdefined';
    }

    public function passByReference(&$foo, array $bar = ['baz' => 303])
    {

    }

    public function optionalNull($baz = null)
    {

    }
}
/**
 * Applies tests to a self defined class.
 */
class SelfDefinedClassTest extends TestCase
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
        assertThat(
                $this->proxy->action(new SelfDefined(), function() {}),
                equals('selfdefined')
        );
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->mapCalls(['action' => 'foo']);
        assertThat(
                $this->proxy->action(new SelfDefined(), function() {}),
                equals('foo')
        );
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['action' => function() { return 'foo'; }]);
        assertThat(
                $this->proxy->action(new SelfDefined(), function() {}),
                equals('foo')
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        verify($this->proxy, 'action')->wasNeverCalled();
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->action(new SelfDefined(), function() {});
        $this->proxy->action(new SelfDefined(), function() {});
        verify($this->proxy, 'action')->wasCalled(2);
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function returnsListOfReceivedArgumentsIfMethodCalled()
    {
        $arg1 = new SelfDefined();
        $arg2 = function() {};
        $this->proxy->action($arg1, $arg2);
        verify($this->proxy, 'action')->received(
                isInstanceOf(SelfDefined::class),
                $arg2
        );
    }
}
