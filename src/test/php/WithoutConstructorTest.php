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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
/**
 * Applies tests to a stub of a class.
 */
class WithoutConstructorTest extends TestCase
{
    private ClassWithConstructor&ClassProxy $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(ClassWithConstructor::class);
    }

    #[Test]
    public function returnsNullIfMethodCallNotMapped(): void
    {
        assertThat($this->proxy->action(), isNull());
    }

    #[Test]
    public function mapToSimpleValueReturnsValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => 3]);
        assertThat($this->proxy->action(), equals(3));
    }

    #[Test]
    public function mapToClosureReturnsClosureReturnValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => fn() => 42]);
        assertThat($this->proxy->action(), equals(42));
    }

    #[Test]
    public function amountOfCallsToMethodIsZeroIfNotCalled(): void
    {
        verify($this->proxy, 'action')->wasNeverCalled();
    }

    #[Test]
    public function recordsAmountOfCallsToMethod(): void
    {
        $this->proxy->action();
        $this->proxy->action();
        verify($this->proxy, 'action')->wasCalled(2);
    }

    #[Test]
    public function returnsListOfReceivedArgumentsIfMethodCalled(): void
    {
        $this->proxy->otherAction(303);
        verify($this->proxy, 'otherAction')->received(303);
    }
}
