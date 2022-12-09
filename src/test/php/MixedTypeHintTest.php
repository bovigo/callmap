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
use Generator;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\{
    assertThat,
    predicate\equals,
};
use function bovigo\callmap\verify;
/**
 * Tests for methods and functions which are declared with type hint mixed.
 *
 * @requires PHP >= 8
 * @since 6.2.0
 * @group typehint
 * @group mixed
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

    public static function acceptableParameters(): Generator
    {
        yield ['string'];
        yield [null];
    }

    /**
     * @test
     * @dataProvider acceptableParameters
     */
    public function unionTypeHintedMethodParamReceivesProperValue(mixed $acceptable): void
    {
        $this->proxy->accept($acceptable);
        verify($this->proxy, 'accept')->received($acceptable);
    }

    public static function acceptableFunctionParameters(): Generator
    {
        yield [[1, 2, 3]];
        yield ['strpos'];
        yield [null];
    }

    /**
     * @test
     * @dataProvider acceptableFunctionParameters
     */
    public function mixedTypeHintedFunctionParamReceivesProperValue(mixed $acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\acceptMixed');
        $foo($acceptable);
        verify($foo)->received($acceptable);
    }

    public static function acceptableFunctionReturnValues(): Generator
    {
        yield [false];
        yield ['example'];
        yield [null];
    }

    /**
     * @test
     * @dataProvider acceptableFunctionReturnValues
     */
    public function mixedTypeHintedFunctionReturnsProperValue(mixed $acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\returnMixed');
        $foo->returns($acceptable);
        assertThat($foo(), equals($acceptable));
    }
}