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
use Countable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
/**
 * Tests for call mapping with a list of return values.
 */
class InvocationResultsTest extends TestCase
{
    private ReflectionObject&ClassProxy $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::of(ReflectionObject::class, [$this]);
    }

    #[Test]
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
     * @since 0.6.0
     */
    #[Test]
    public function mapToInvocationResultsWithCallableReturnsResultOfCallable(): void
    {
        $this->proxy->returns([
            'getName' => onConsecutiveCalls(function() { return 'foo'; })
        ]);
        assertThat($this->proxy->getName(), equals('foo'));
    }

    #[Test]
    public function invocationResultIsResultOfOriginalMethodIfCalledMoreOftenThenResultsDefined(): void
    {
        $this->proxy->returns(['getName' => onConsecutiveCalls('foo')]);
        assertThat($this->proxy->getName(), equals('foo'));
        assertThat($this->proxy->getName(), equals(__CLASS__));
    }

    /**
     * @since 0.6.0
     */
    #[Test]
    public function invocationResultIsNullForStubIfCalledMoreOftenThenResultsDefined(): void
    {
        $proxy = NewInstance::stub(OneMoreSelfDefined::class);
        $proxy->returns(['getName' => onConsecutiveCalls('foo')]);
        $proxy->getName(); // foo
        assertThat($proxy->getName(), isNull());
    }

    /**
     * @since 0.6.0
     */
    #[Test]
    public function invocationResultIsNullForInterfaceIfCalledMoreOftenThenResultsDefined(): void
    {
        $proxy = NewInstance::stub(Countable::class);
        $proxy->returns(['count' => onConsecutiveCalls(303)]);
        $proxy->count(); // 303
        assertThat($proxy->count(), isNull());
    }
}
