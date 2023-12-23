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

use bovigo\callmap\helper\AnotherTestHelperClass;
use bovigo\callmap\helper\ClassWithUnionTypeHints;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TypeError;

use function bovigo\assert\{
    assertThat,
    expect,
    predicate\contains,
    predicate\equals,
    predicate\isSameAs
};
use function bovigo\callmap\verify;
/**
 * Tests for method and functions which are declared with union type hints.
 *
 * @since 6.2.0
 */
#[Group('typehint')]
#[Group('union')]
class UnionTypeHintTest extends TestCase
{
    /**
     * @var ClassWithUnionTypeHints&ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(ClassWithUnionTypeHints::class);
    }

    public static function acceptableParameters(): Generator
    {
        yield ['string'];
        yield [new AnotherTestHelperClass()];
    }

    /**
     * @param string|AnotherTestHelperClass $acceptable
     */
    #[Test]
    #[DataProvider('acceptableParameters')]
    public function unionTypeHintedMethodParamReceivesProperValue(
        string|AnotherTestHelperClass $acceptable
    ): void {
        $this->proxy->accept($acceptable);
        verify($this->proxy, 'accept')->received($acceptable);
    }

    #[Test]
    public function unionTypeHintedMethodParamThrowsTypeErrorWhenWrongTypeReceived(): void
    {
        expect(fn() => $this->proxy->accept(3.03))
             ->throws(TypeError::class)
             ->message(contains('Argument #1 ($something) must be of type bovigo\callmap\helper\AnotherTestHelperClass|string, float given'));
    }

    #[Test]
    public function unionParameterTypeHintWithSelfAcceptsInstanceOfClass(): void
    {
        $self = new ClassWithUnionTypeHints();
        $this->proxy->methodWithSelfParam($self);
        verify($this->proxy, 'methodWithSelfParam')->received($self);
    }

    #[Test]
    public function unionParameterTypeHintOnParamWithSelfAcceptsInstanceOfProxy(): void
    {
        $this->proxy->methodWithSelfParam($this->proxy);
        verify($this->proxy, 'methodWithSelfParam')->received($this->proxy);
    }

    #[Test]
    public function unionReturnTypeHintWithSelfCanReturnNativeInstance(): void
    {
        $self = new ClassWithUnionTypeHints();
        $this->proxy->returns(['methodReturningSelf' => $self]);
        assertThat($this->proxy->methodReturningSelf(), isSameAs($self));
    }

    #[Test]
    public function unionReturnTypeHintWithSelfCanReturnProxy(): void
    {
        $this->proxy->returns(['methodReturningSelf' => $this->proxy]);
        assertThat($this->proxy->methodReturningSelf(), isSameAs($this->proxy));
    }

    /**
     * @return \Generator<array<int|float>>
     */
    public static function acceptableReturnValues(): \Generator
    {
        yield [1];
        yield [3.03];
    }

    /**
     * @param int|float $returnValue
     */
    #[Test]
    #[DataProvider('acceptableReturnValues')]
    public function unionTypeHintedMethodReturnsProperValue($returnValue): void
    {
        $this->proxy->returns(['doReturn' => $returnValue]);
        assertThat($this->proxy->doReturn(), equals($returnValue));
    }

    #[Test]
    public function unionTypeHintedMethodThrowsTypeErrorWithInvalidReturnValue(): void
    {
        $this->proxy->returns(['doReturn' => 'something invalid']);
        expect(fn() => $this->proxy->doReturn())
             ->throws(TypeError::class)
             ->message(contains('Return value must be of type int|float, string returned'));
    }

    public static function acceptableFunctionParameters(): Generator
    {
        yield [[1, 2, 3]];
        yield ['strpos'];
        yield [null];
    }

    #[Test]
    #[DataProvider('acceptableFunctionParameters')]
    public function unionTypeHintedFunctionParamReceivesProperValue(mixed $acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithUnionTypeHints');
        $foo($acceptable);
        verify($foo)->received($acceptable);
    }

    public static function acceptableFunctionReturnValues(): Generator
    {
        yield [false];
        yield ['example'];
        yield [null];
    }

    #[Test]
    #[DataProvider('acceptableFunctionReturnValues')]
    public function unionTypeHintedFunctionReturnsProperValue(mixed $acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithUnionTypeHints');
        $foo->returns($acceptable);
        assertThat($foo(), equals($acceptable));
    }
}
