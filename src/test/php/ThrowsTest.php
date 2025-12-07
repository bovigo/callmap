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

use bovigo\callmap\helper\SomeClassWithMethodReturningVoid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionObject;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\contains;
use function bovigo\assert\predicate\equals;
/**
 * Test for bovigo\callmap\throws()
 *
 * @since 0.2.0
 */
class ThrowsTest extends TestCase
{
    private ReflectionObject&ClassProxy $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::of(ReflectionObject::class, [$this]);
    }

    #[Test]
    public function throwsExceptionPassedViaThrows(): void
    {
        $e = new ReflectionException('some error');
        $this->proxy->returns(['getName' => throws($e)]);
        expect(fn() => $_ = $this->proxy->getName())
            ->throws(ReflectionException::class)
            ->message(contains('some error'));

    }

    #[Test]
    public function throwsExceptionPassedViaInvocationResults(): void
    {
        $e = new ReflectionException('some error');
        $this->proxy->returns(
            ['getName' => onConsecutiveCalls('foo', throws($e))]
        );
        assertThat($this->proxy->getName(), equals('foo'));
        expect(fn() => $_ = $this->proxy->getName()) // throws $e
            ->throws(ReflectionException::class)
            ->message(contains('some error'));
    }

    /**
     * @since 9.1.0
     */
    public static function provideNonReturningClass(): iterable
    {
        yield 'does not return with void' => [
            SomeClassWithMethodReturningVoid::class, 'returnNothing'
        ];
        yield 'does not return with never' => [
            SomeClassWithMethodReturningVoid::class, 'returnNever'
        ];
    }

    /**
     * @since 9.1.0
     */
    #[Test]
    #[DataProvider('provideNonReturningClass')]
    #[Group('issue_120')]
    public function canThrowExceptionFromMethodReturningVoid(
        string $nonReturningClass,
        string $nonReturningMethod
    ): void {
        $proxy = NewInstance::of($nonReturningClass)->returns([
            $nonReturningMethod => throws(new ReflectionException('some error'))
        ]);

        expect(fn() => $_ = $proxy->$nonReturningMethod()) // throws $e
            ->throws(ReflectionException::class)
            ->message(contains('some error'));
    }
}
