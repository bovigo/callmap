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

use function bovigo\assert\{
    assertNull,
    assertThat,
    expect,
    predicate\equals,
    predicate\isInstanceOf,
    predicate\isNotSameAs
};
/**
 * Helper function for the test.
 */
function doSomething(): string
{
    return 'did something';
}
/**
 * Helper function for the test.
 */
function greet(string $whom)
{
    return 'Hello ' . $whom;
}
/**
 * All remaining tests for bovigo\callmap\NewCallable.
 *
 * @since  3.1.0
 */
class NewCallableTest extends TestCase
{
    /**
     * @test
     */
    public function callWithNonExistingFunctionNameThrowsReflectionException()
    {
        expect(function() { NewCallable::of('doesNotExist'); })
                ->throws(\ReflectionException::class);
    }

    /**
     * @test
     */
    public function doesNotGenerateClassTwice()
    {
        assertThat(
                NewCallable::classname('substr'),
                equals(NewCallable::classname('substr'))
        );
    }

    /**
     * @test
     */
    public function doesCreateIndependentInstances()
    {
        assertThat(
                NewCallable::of('substr'),
                isNotSameAs(NewCallable::of('substr'))
        );
    }

    /**
     * @test
     */
    public function doesCreateIndependentStubs()
    {
        assertThat(
                NewCallable::stub('substr'),
                isNotSameAs(NewCallable::stub('substr'))
        );
    }

    /**
     * @test
     */
    public function canCreateInstanceFromFunctionWithPhp7ReturnTypeHint()
    {
        assertThat(
                NewCallable::of('bovigo\callmap\doSomething'),
                isInstanceOf(FunctionProxy::class)
        );
    }

    public function functionNames(): array
    {
        return [['strlen', 5], ['bovigo\callmap\greet', 'Hello world']];
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function callsOriginalFunctionWhenNotMapped($functionName, $expected)
    {
        $function = NewCallable::of($functionName);
        assertThat($function('world'), equals($expected));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function stubsDoNotCallOriginalFunctionWhenNotMapped($functionName)
    {
        $function = NewCallable::stub($functionName);
        assertNull($function('world'));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function mapReturnValueToNullShouldNotCallOriginalFunction($functionName)
    {
        $function = NewCallable::of($functionName)->returns(null);
        assertNull($function('world'));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function mapReturnValueReturnsMappedValueOnInvocation($functionName)
    {
        $function = NewCallable::of($functionName)->returns('great stuff');
        assertThat($function('world'), equals('great stuff'));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function canMapWithConsecutiveCalls($functionName, $expected)
    {
        $function = NewCallable::of($functionName)
                ->returns(onConsecutiveCalls('great', 'stuff'));
        assertThat($function('world'), equals('great'));
        assertThat($function('world'), equals('stuff'));
        assertThat($function('world'), equals($expected));
    }

    /**
     * @test
     * @dataProvider  functionNames
     */
    public function canMapWithThrows($functionName)
    {
        $function = NewCallable::of($functionName)
                ->throws(new \RuntimeException('failure'));
        expect(function() use ($function) { $function('world'); })
                ->throws(\RuntimeException::class)
                ->withMessage('failure');
    }
}
