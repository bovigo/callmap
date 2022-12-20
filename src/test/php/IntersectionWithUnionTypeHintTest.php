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

use bovigo\callmap\helper\{
    ClassWithIntersectionAndUnionTypeHints,
    ClassWithIntersectionTypeHints
};
use Generator;
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
 * @requires PHP >= 8.2
 * @since 7.0.0
 * @group typehint
 * @group intersection
 * @group union
 */
class ClassWithIntersectionAndUnionTypeHintsTest extends TestCase
{
    /** @var ClassWithIntersectionAndUnionTypeHints&ClassProxy */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(ClassWithIntersectionAndUnionTypeHints::class);
    }

    public function acceptableValues(): Generator
    {
        yield [null];
        yield [new ClassWithIntersectionTypeHints()];
    }

    /**
     * @test
     * @dataProvider acceptableValues
     */
    public function intersectionTypeHintedMethodParamReceivesProperValue($acceptable): void
    {
        $this->proxy->mayAccept($acceptable);
        verify($this->proxy, 'mayAccept')->received($acceptable);
    }

    /**
     * @test
     */
    public function intersectionTypeHintedMethodParamThrowsTypeErrorWhenWrongTypeReceived(): void
    {
        expect(fn() => $this->proxy->mayAccept(3.03))
             ->throws(TypeError::class)
             ->message(contains('Argument #1 ($acceptable) must be of type (bovigo\callmap\helper\A&bovigo\callmap\helper\B)|null, float given'));
    }

    /**
     * @test
     * @dataProvider acceptableValues
     */
    public function intersectionReturnTypeHintedMethodReturnsProperValue($acceptable): void
    {
        $this->proxy->returns(['mayReturn' => $acceptable]);

        assertThat($this->proxy->mayReturn(), equals($acceptable));
    }

    /**
     * @test
     */
    public function intersectionReturnTypeHintedMethodThrowsTypeErrorOnWrongValue(): void
    {
        $this->proxy->returns(['mayReturn' => 3.03]);

        expect(fn() => $this->proxy->mayReturn())
             ->throws(TypeError::class)
             ->message(contains('Return value must be of type (bovigo\callmap\helper\A&bovigo\callmap\helper\B)|null, float returned'));
    }

    /**
     * @test
     */
    public function intersectionTypeHintedFunctionParamReceivesProperValue(): void
    {
        $aWithB = new ClassWithIntersectionTypeHints();
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithIntersectionAndUnionTypeHints');
        $foo($aWithB);
        verify($foo)->received($aWithB);
    }

    /**
     * @test
     */
    public function intersectionTypeHintedFunctionParamThrowsTypeErrorWhenWrongTypeReceived(): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithIntersectionAndUnionTypeHints');
        expect(fn() => $foo(3.03))
             ->throws(TypeError::class)
             ->message(contains('Argument #1 ($extra) must be of type (bovigo\callmap\helper\A&bovigo\callmap\helper\B)|null, float given'));
    }

    public function intersectionTypeHintedFunctionReturnsProperValue(): void
    {
        $aWithB = new ClassWithIntersectionTypeHints();
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithIntersectionAndUnionTypeHints');
        $foo->returns($aWithB);

        assertThat($foo(), isSameAs($aWithB));
    }

    /**
     * @test
     */
    public function intersectionReturnTypeHintedMethodThrowsTypeErrorOnWrongReturn(): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithIntersectionAndUnionTypeHints');
        $foo->returns(3.03);

        expect(fn() => $foo(new ClassWithIntersectionTypeHints()))
             ->throws(TypeError::class)
             ->message(contains('Return value must be of type (bovigo\callmap\helper\A&bovigo\callmap\helper\B)|null, float returned'));
    }
}
