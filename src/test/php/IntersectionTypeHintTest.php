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

use bovigo\callmap\helper\{A, B, ClassWithIntersectionTypeHints};
use PHPUnit\Framework\TestCase;
use TypeError;

use function bovigo\assert\{
    assertThat,
    expect,
    predicate\contains,
    predicate\isSameAs
};
use function bovigo\callmap\verify;
/**
 * Tests for method and functions which are declared with union type hints.
 *
 * @requires PHP >= 8.1
 * @since 7.0.0
 * @group typehint
 * @group intersection
 */
class IntersectionTypeHintTest extends TestCase
{
    /** @var ClassWithIntersectionTypeHints&ClassProxy */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(ClassWithIntersectionTypeHints::class);
    }

    /**
     * @test
     */
    public function intersectionTypeHintedMethodParamReceivesProperValue(): void
    {
        $aWithB = new ClassWithIntersectionTypeHints();
        $this->proxy->accept($aWithB);
        verify($this->proxy, 'accept')->received($aWithB);
    }

    /**
     * @test
     */
    public function intersectionTypeHintedMethodParamThrowsTypeErrorWhenWrongTypeReceived(): void
    {
        expect(fn() => $this->proxy->accept(3.03))
             ->throws(TypeError::class)
             ->message(contains('Argument #1 ($acceptable) must be of type bovigo\callmap\helper\A&bovigo\callmap\helper\B, float given'));
    }

    /**
     * @test
     */
    public function intersectionReturnTypeHintedMethodReturnsProperValue(): void
    {
        $this->proxy->returns(['doReturn' => $this->proxy]);

        $this->assertSame($this->proxy, $this->proxy->doReturn());
    }

    /**
     * @test
     */
    public function intersectionReturnTypeHintedMethodThrowsTypeErrorOnWrongValue(): void
    {
        $this->proxy->returns(['doReturn' => 3.03]);

        expect(fn() => $this->proxy->doReturn())
             ->throws(TypeError::class)
             ->message(contains('Return value must be of type bovigo\callmap\helper\A&bovigo\callmap\helper\B, float returned'));
    }

    /**
     * @test
     */
    public function intersectionTypeHintedFunctionParamReceivesProperValue(): void
    {
        $aWithB = new ClassWithIntersectionTypeHints();
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithIntersectionTypeHints');
        $foo($aWithB);
        verify($foo)->received($aWithB);
    }

    /**
     * @test
     */
    public function intersectionTypeHintedFunctionParamThrowsTypeErrorWhenWrongTypeReceived(): void
    {
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithIntersectionTypeHints');
        expect(fn() => $foo(3.03))
             ->throws(TypeError::class)
             ->message(contains('Argument #1 ($extra) must be of type bovigo\callmap\helper\A&bovigo\callmap\helper\B, float given'));
    }

    public function intersectionTypeHintedFunctionReturnsProperValue(): void
    {
        $aWithB = new ClassWithIntersectionTypeHints();
        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithIntersectionTypeHints');
        $foo->returns($aWithB);

        assertThat($foo(), isSameAs($aWithB));
    }

    /**
     * @test
     */
    public function intersectionReturnTypeHintedMethodThrowsTypeErrorOnWrongReturn(): void
    {

        $foo = NewCallable::of('bovigo\callmap\helper\exampleFunctionWithIntersectionTypeHints');
        $foo->returns(3.03);

        expect(fn() => $foo(new ClassWithIntersectionTypeHints()))
             ->throws(TypeError::class)
             ->message(contains('Return value must be of type bovigo\callmap\helper\A&bovigo\callmap\helper\B, float returned'));
    }
}
