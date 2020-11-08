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
use bovigo\callmap\helper\OneMoreSelfDefined;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
/**
 * Tests for call mapping with a list of return values.
 */
class InvocationResultsTest extends TestCase
{
    /**
     * @var  \ReflectionObject&\bovigo\callmap\ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::of(\ReflectionObject::class, [$this]);
    }

    /**
     * @test
     */
    public function mapToInvocationResultsReturnsResultOnMethodCall(): void
    {
        $this->proxy->returns(
            ['getName' => onConsecutiveCalls('foo', 'bar', 'baz')]
        );
        foreach (['foo', 'bar', 'baz'] as $expected) {
            assertThat($this->proxy->getName(), equals($expected));
        }
    }

    /**
     * @test
     * @since  0.6.0
     */
    public function mapToInvocationResultsWithCallableReturnsResultOfCallable(): void
    {
        $this->proxy->returns([
            'getName' => onConsecutiveCalls(function() { return 'foo'; })
        ]);
        assertThat($this->proxy->getName(), equals('foo'));
    }

    /**
     * @test
     */
    public function invocationResultIsResultOfOriginalMethodIfCalledMoreOftenThenResultsDefined(): void
    {
        $this->proxy->returns(['getName' => onConsecutiveCalls('foo')]);
        $this->proxy->getName(); // foo
        assertThat($this->proxy->getName(), equals(__CLASS__));
    }

    /**
     * @test
     * @since  0.6.0
     */
    public function invocationResultIsNullForStubIfCalledMoreOftenThenResultsDefined(): void
    {
        $proxy = NewInstance::stub(OneMoreSelfDefined::class);
        $proxy->returns(['getName' => onConsecutiveCalls('foo')]);
        $proxy->getName(); // foo
        assertThat($proxy->getName(), isNull());
    }

    /**
     * @test
     * @since  0.6.0
     */
    public function invocationResultIsNullForInterfaceIfCalledMoreOftenThenResultsDefined(): void
    {
        $proxy = NewInstance::stub(\Countable::class);
        $proxy->returns(['count' => onConsecutiveCalls(303)]);
        $proxy->count(); // 303
        assertThat($proxy->count(), isNull());
    }
}
