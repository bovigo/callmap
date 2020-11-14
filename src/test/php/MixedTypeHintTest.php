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
use bovigo\callmap\helper\ClassWithMixedTypeHints;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\{
    assertThat,
    predicate\equals,
};
use function bovigo\callmap\verify;
/**
 * Tests for functions which are declared with return type void.
 *
 * @requires PHP >= 8
 * @since  6.2.0
 * @group  typehint
 * @group  mixed
 */
class MixedTypeHintTest extends TestCase
{
    /**
     * @var  ClassWithMixedTypeHints&\bovigo\callmap\ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(ClassWithMixedTypeHints::class);
    }

    /**
     * @return \Generator<array<mixed>>
     */
    public static function acceptableParameters(): \Generator
    {
        yield ['string'];
        yield [null];
    }

    /**
     * @test
     * @dataProvider acceptableParameters
     * @param mixed $acceptable
     */
    public function unionTypeHintedMethodParamReceivesProperValue(mixed $acceptable): void
    {
        $this->proxy->accept($acceptable);
        verify($this->proxy, 'accept')->received($acceptable);
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
     * @param mixed $acceptable
     */
    public function mixedTypeHintedFunctionParamReceivesProperValue($acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\acceptMixed');
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
    public function mixedTypeHintedFunctionReturnsProperValue($acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\returnMixed');
        $foo->returns($acceptable);
        assertThat($foo(), equals($acceptable));
    }
}