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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\{
    assertThat,
    predicate\equals,
};
use function bovigo\callmap\verify;
/**
 * Tests for methods and functions which are declared with type hint mixed.
 *
 * @since 6.2.0
 */
#[Group('typehint')]
#[Group('mixed')]
class MixedTypeHintTest extends TestCase
{
    private ClassWithMixedTypeHints&ClassProxy $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(ClassWithMixedTypeHints::class);
    }

    public static function acceptableParameters(): Generator
    {
        yield ['string'];
        yield [null];
    }

    #[Test]
    #[DataProvider('acceptableParameters')]
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

    #[Test]
    #[DataProvider('acceptableParameters')]
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

    #[Test]
    #[DataProvider('acceptableParameters')]
    public function mixedTypeHintedFunctionReturnsProperValue(mixed $acceptable): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\returnMixed');
        $foo->returns($acceptable);
        assertThat($foo(), equals($acceptable));
    }
}