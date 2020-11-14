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
use PHPUnit\Framework\TestCase;

use function bovigo\assert\{
    assertThat,
    expect,
    predicate\contains,
    predicate\equals
};
use function bovigo\callmap\verify;
/**
 * Tests for functions which are declared with return type void.
 *
 * @requires PHP >= 8
 * @since  6.2.0
 * @group  typehint
 * @group  union
 */
class UnionTypeHintTest extends TestCase
{
    /**
     * @var  ClassWithUnionTypeHints&\bovigo\callmap\ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(ClassWithUnionTypeHints::class);
    }

    /**
     * @return \Generator<array<string|AnotherTestHelperClass>>
     */
    public static function acceptableParameters(): \Generator
    {
        yield ['string'];
        yield [new AnotherTestHelperClass()];
    }

    /**
     * @test
     * @dataProvider acceptableParameters
     * @param string|AnotherTestHelperClass $acceptable
     */
    public function unionTypeHintedMethodParamReceivesProperValue($acceptable): void
    {
        $this->proxy->accept($acceptable);
        verify($this->proxy, 'accept')->received($acceptable);
    }

    /**
     * @test
     */
    public function unionTypeHintedMethodParamThrowsTypeErrorWhenWrongTypeReceived(): void
    {
        expect(function() { $this->proxy->accept(3.03); })
             ->throws(\TypeError::class)
             ->message(contains('Argument #1 ($something) must be of type bovigo\callmap\helper\AnotherTestHelperClass|string, float given'));
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
     * @test
     * @dataProvider acceptableReturnValues
     * @param int|float $returnValue
     */
    public function unionTypeHintedMethodReturnsProperValue($returnValue): void
    {
        $this->proxy->returns(['doReturn' => $returnValue]);
        assertThat($this->proxy->doReturn(), equals($returnValue));
    }

    /**
     * @test
     */
    public function unionTypeHintedMethodThrowsTypeErrorWithInvalidReturnValue(): void
    {
        $this->proxy->returns(['doReturn' => 'something invalid']);
        expect(function() { $this->proxy->doReturn(); })
             ->throws(\TypeError::class)
             ->message(contains('Return value must be of type int|float, string returned'));
    }

    /**
     * @return \Generator<array<mixed>>
     */
    public static function acceptableFunctionParameters(): \Generator
    {
        yield [[1, 2, 3]];
        yield ['strpos'];
        yield [null];
    }

    /**
     * @test
     * @dataProvider acceptableFunctionParameters
     * @param string|ClassWithUnionTypeHints $acceptable
     */
    public function unionTypeHintedFunctionParamReceivesProperValue($acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithUnionTypeHints');
        $foo($acceptable);
        verify($foo)->received($acceptable);
    }

    /**
     * @return \Generator<array<mixed>>
     */
    public static function acceptableFunctionReturnValues(): \Generator
    {
        yield [false];
        yield ['example'];
        yield [null];
    }

    /**
     * @test
     * @dataProvider acceptableFunctionReturnValues
     * @param false|string $acceptable
     */
    public function unionTypeHintedFunctionReturnsProperValue($acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithUnionTypeHints');
        $foo->returns($acceptable);
        assertThat($foo(), equals($acceptable));
    }
}
