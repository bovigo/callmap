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
use bovigo\callmap\helper\SelfDefined;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Applies tests to a self defined class.
 */
class SelfDefinedClassTest extends TestCase
{
    /**
     * @var  SelfDefined&\bovigo\callmap\ClassProxy
     */
    private $proxy;

    public function setUp(): void
    {
        $this->proxy = NewInstance::of(new SelfDefined());
    }

    /**
     * @test
     */
    public function callsOriginalMethodIfNoMappingProvided(): void
    {
        assertThat(
                $this->proxy->action(new SelfDefined(), function() {}),
                equals('selfdefined')
        );
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => 'foo']);
        assertThat(
                $this->proxy->action(new SelfDefined(), function() {}),
                equals('foo')
        );
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => function() { return 'foo'; }]);
        assertThat(
                $this->proxy->action(new SelfDefined(), function() {}),
                equals('foo')
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled(): void
    {
        verify($this->proxy, 'action')->wasNeverCalled();
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function recordsAmountOfCallsToMethod(): void
    {
        $this->proxy->action(new SelfDefined(), function() {});
        $this->proxy->action(new SelfDefined(), function() {});
        verify($this->proxy, 'action')->wasCalled(2);
    }

    /**
     * @test
     */
    public function returnsListOfReceivedArgumentsIfMethodCalled(): void
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
