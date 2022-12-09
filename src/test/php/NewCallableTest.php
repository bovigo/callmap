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

use Generator;
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
    /**
     * @test
     */
    public function callWithNonExistingFunctionNameThrowsReflectionException(): void
    {
        expect(fn() => NewCallable::of('doesNotExist'))
            ->throws(\ReflectionException::class);
    }

    /**
     * @test
     */
    public function doesNotGenerateClassTwice(): void
    {
        assertThat(
            NewCallable::classname('substr'),
            equals(NewCallable::classname('substr'))
        );
    }

    /**
     * @test
     */
    public function doesCreateIndependentInstances(): void
    {
        assertThat(
            NewCallable::of('substr'),
            isNotSameAs(NewCallable::of('substr'))
        );
    }

    /**
     * @test
     */
    public function doesCreateIndependentStubs(): void
    {
        assertThat(
            NewCallable::stub('substr'),
            isNotSameAs(NewCallable::stub('substr'))
        );
    }

    /**
     * @test
     */
    public function canCreateInstanceFromFunctionWithPhp7ReturnTypeHint(): void
    {
        assertThat(
            NewCallable::of('bovigo\callmap\helper\doSomething'),
            isInstanceOf(FunctionProxy::class)
        );
    }

    public function functionNames(): Generator
    {
        yield ['strtoupper', 'WORLD'];
        yield ['bovigo\callmap\helper\greet', 'Hello world'];
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function callsOriginalFunctionWhenNotMapped(
        string $functionName,
        string $expected
    ): void {
        $function = NewCallable::of($functionName);
        assertThat($function('world'), equals($expected));
    }

    /**
     * @test
     */
    public function stubsDoNotCallOriginalFunctionWhenNotMapped(): void
    {
        $function = NewCallable::stub('bovigo\callmap\helper\greet');
        assertNull($function('world'));
    }

    /**
     * @test
     */
    public function mapReturnValueToNullShouldNotCallOriginalFunction(): void
    {
        $function = NewCallable::of('bovigo\callmap\helper\greet')->returns(null);
        assertNull($function('world'));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function mapReturnValueReturnsMappedValueOnInvocation(string $functionName): void
    {
        $function = NewCallable::of($functionName)->returns('great stuff');
        assertThat($function('world'), equals('great stuff'));
    }

    /**
     * @test
     * @dataProvider functionNames
     */
    public function canMapWithConsecutiveCalls(string $functionName, string $expected): void
    {
        $function = NewCallable::of($functionName)
            ->returns(onConsecutiveCalls('great', 'stuff'));
        assertThat($function('world'), equals('great'));
        assertThat($function('world'), equals('stuff'));
        assertThat($function('world'), equals($expected));
    }

    /**
     * @test
     * @dataProvider functionNames
     */
    public function canMapWithThrows(string $functionName): void
    {
        $function = NewCallable::of($functionName)
            ->throws(new \RuntimeException('failure'));
        expect(fn() => $function('world'))
            ->throws(\RuntimeException::class)
            ->withMessage('failure');
    }

    /**
     * @test
     * @group optional_return_value
     * @since 5.0.2
     */
    public function canWorkWithOptionalReturnTypehints(): void
    {
        $function = NewCallable::of('\bovigo\callmap\helper\withOptionalReturnValue');
        assertNull($function());
    }

    /**
     * @test
     * @group optional_return_value
     * @since 5.1.0
     */
    public function canWorkWithOptionalBuiltinReturnTypehints(): void
    {
        $function = NewCallable::of('\bovigo\callmap\helper\someOptionalString');
        assertNull($function());
    }

    /**
     * @test
     * @group void_return
     * @since 5.1.0
     */
    public function canWorkWithVoidReturnTypehints(): void
    {
        $function = NewCallable::of('\bovigo\callmap\helper\returnsVoid');
        assertNull($function());
    }
}
