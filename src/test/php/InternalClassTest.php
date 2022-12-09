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
use ReflectionObject;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
/**
 * Applies tests to a PHP internal class.
 */
class InternalClassTest extends TestCase
{
    /**
     * @var ReflectionObject&ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::of(ReflectionObject::class, [$this]);
    }

    /**
     * @test
     */
    public function callsOriginalMethodIfNoMappingProvided(): void
    {
        assertThat($this->proxy->getName(), equals(__CLASS__));
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall(): void
    {
        $this->proxy->returns(['getName' => 'foo']);
        assertThat($this->proxy->getName(), equals('foo'));
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall(): void
    {
        $this->proxy->returns(['getName' => function() { return 'foo'; }]);
        assertThat($this->proxy->getName(), equals('foo'));
    }

    /**
     * @test
     * @since  0.4.0
     */
    public function mapToCallableReturnsCallableReturnValueOnMethodCall(): void
    {
        // doesn't make much sense with ReflectionObject, but too lazy create
        // a proper example
        $this->proxy->returns(['getStaticPropertyValue' => 'strtoupper']);
        assertThat($this->proxy->getStaticPropertyValue('foo'), equals('FOO'));
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled(): void
    {
        verify($this->proxy, 'getNamespaceName')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod(): void
    {
        $_ = $this->proxy->getName();
        $_ = $this->proxy->getName();
        $_ = $this->proxy->getShortName();
        verify($this->proxy,'getName')->wasCalled(2);
        verify($this->proxy, 'getShortName')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function canVerifyReceivedArguments(): void
    {
        $this->proxy->implementsInterface(Proxy::class);
        verify($this->proxy, 'implementsInterface')->received(Proxy::class);
    }

    /**
     * @test
     */
    public function canVerifyReceivedArgumentsOfSpecificInvocation(): void
    {
        $this->proxy->hasProperty('foo');
        $this->proxy->hasProperty('bar');
        verify($this->proxy, 'hasProperty')->receivedOn(2, 'bar');
    }
}
