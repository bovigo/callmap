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

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\{
    assertNull,
    assertThat,
    expect,
    predicate\equals,
    predicate\isInstanceOf,
    predicate\isNotSameAs
};
use function bovigo\callmap\helper\{doSomething, greet};
/**
 * All remaining tests for bovigo\callmap\NewCallable.
 *
 * @since 3.1.0
 */
class NewCallableTest extends TestCase
{
    #[Test]
    public function callWithNonExistingFunctionNameThrowsReflectionException(): void
    {
        expect(fn() => NewCallable::of('doesNotExist'))
            ->throws(\ReflectionException::class);
    }

    #[Test]
    public function doesNotGenerateClassTwice(): void
    {
        assertThat(
            NewCallable::classname('substr'),
            equals(NewCallable::classname('substr'))
        );
    }

    #[Test]
    public function doesCreateIndependentInstances(): void
    {
        assertThat(
            NewCallable::of('substr'),
            isNotSameAs(NewCallable::of('substr'))
        );
    }

    #[Test]
    public function doesCreateIndependentStubs(): void
    {
        assertThat(
            NewCallable::stub('substr'),
            isNotSameAs(NewCallable::stub('substr'))
        );
    }

    #[Test]
    public function canCreateInstanceFromFunctionWithPhp7ReturnTypeHint(): void
    {
        assertThat(
            NewCallable::of('bovigo\callmap\helper\doSomething'),
            isInstanceOf(FunctionProxy::class)
        );
    }

    public static function functionNames(): iterable
    {
        foreach (static::functionNamesWithExpectedReturnValue() as $key => $arguments) {
            yield $key => [$arguments[0]];
        }
    }

    public static function functionNamesWithExpectedReturnValue(): iterable
    {
        yield ['strtoupper', 'WORLD'];
        yield ['bovigo\callmap\helper\greet', 'Hello world'];
    }

    #[Test]
    #[DataProvider('functionNamesWithExpectedReturnValue')]
    public function callsOriginalFunctionWhenNotMapped(
        string $functionName,
        string $expected
    ): void {
        $function = NewCallable::of($functionName);
        assertThat($function('world'), equals($expected));
    }

    #[Test]
    public function stubsDoNotCallOriginalFunctionWhenNotMapped(): void
    {
        $function = NewCallable::stub('bovigo\callmap\helper\greet');
        assertNull($function('world'));
    }

    #[Test]
    public function mapReturnValueToNullShouldNotCallOriginalFunction(): void
    {
        $function = NewCallable::of('bovigo\callmap\helper\greet')->returns(null);
        assertNull($function('world'));
    }

    #[Test]
    #[DataProvider('functionNames')]
    public function mapReturnValueReturnsMappedValueOnInvocation(string $functionName): void
    {
        $function = NewCallable::of($functionName)->returns('great stuff');
        assertThat($function('world'), equals('great stuff'));
    }

    #[Test]
    #[DataProvider('functionNamesWithExpectedReturnValue')]
    public function canMapWithConsecutiveCalls(string $functionName, string $expected): void
    {
        $function = NewCallable::of($functionName)
            ->returns(onConsecutiveCalls('great', 'stuff'));
        assertThat($function('world'), equals('great'));
        assertThat($function('world'), equals('stuff'));
        assertThat($function('world'), equals($expected));
    }

    #[Test]
    #[DataProvider('functionNames')]
    public function canMapWithThrows(string $functionName): void
    {
        $function = NewCallable::of($functionName)
            ->throws(new \RuntimeException('failure'));
        expect(fn() => $function('world'))
            ->throws(\RuntimeException::class)
            ->withMessage('failure');
    }

    /**
     * @since 5.0.2
     */
    #[Test]
    #[Group('optional_return_value')]
    public function canWorkWithOptionalReturnTypehints(): void
    {
        $function = NewCallable::of('\bovigo\callmap\helper\withOptionalReturnValue');
        assertNull($function());
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('optional_return_value')]
    public function canWorkWithOptionalBuiltinReturnTypehints(): void
    {
        $function = NewCallable::of('\bovigo\callmap\helper\someOptionalString');
        assertNull($function());
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('void_return')]
    public function canWorkWithVoidReturnTypehints(): void
    {
        $function = NewCallable::of('\bovigo\callmap\helper\returnsVoid');
        assertNull($function());
    }

    /**
     * @since 8.0.0
     */
    #[Test]
    #[Group('never_return')]
    public function canWorkWithNeverReturnTypehints(): void
    {
        $function = NewCallable::of('\bovigo\callmap\helper\returnsNever');

        // force throw, test is mainly to ensure a function with return type
        // never can be mocked
        expect($function)->throws(Exception::class);
    }
}
