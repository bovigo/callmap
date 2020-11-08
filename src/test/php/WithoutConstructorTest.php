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
use bovigo\callmap\helper\ClassWithConstructor;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
/**
 * Applies tests to a stub of a class.
 */
class WithoutConstructorTest extends TestCase
{
    /**
     * @var  ClassWithConstructor&\bovigo\callmap\ClassProxy
     */
    private $proxy;

    /**
     * set up test environment
     */
    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(ClassWithConstructor::class);
    }
    /**
     * @test
     */
    public function returnsNullIfMethodCallNotMapped(): void
    {
        assertThat($this->proxy->action(), isNull());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => 3]);
        assertThat($this->proxy->action(), equals(3));
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => function() { return 42; }]);
        assertThat($this->proxy->action(), equals(42));
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled(): void
    {
        verify($this->proxy, 'action')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod(): void
    {
        $this->proxy->action();
        $this->proxy->action();
        verify($this->proxy, 'action')->wasCalled(2);
    }

    /**
     * @test
     */
    public function returnsListOfReceivedArgumentsIfMethodCalled(): void
    {
        $this->proxy->otherAction(303);
        verify($this->proxy, 'otherAction')->received(303);
    }
}
