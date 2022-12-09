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

use bovigo\callmap\helper\ClassWithStaticReturnTypeHint;
use bovigo\callmap\helper\ExtendedClassWithStaticReturnTypeHint;
use Generator;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\{
    assertThat,
    predicate\isSameAs
};
/**
 * Tests for methods which are declared with return type static.
 *
 * @requires PHP >= 8
 * @since 6.2.0
 * @group typehint
 * @group static
 */
class StaticReturnTypeHintTest extends TestCase
{
    public static function classesWithStaticReturnTypes(): Generator
    {
        yield [ClassWithStaticReturnTypeHint::class];
        yield [ExtendedClassWithStaticReturnTypeHint::class];
    }

    /**
     * @test
     * @dataProvider  classesWithStaticReturnTypes
     * @param  class-string  $class
     */
    public function stubReturnsItselfWhenReturnTypeHintIsStatic(string $class): void
    {
        /** @var ClassWithStaticReturnTypeHint&ClassProxy $static */
        $static = NewInstance::stub($class);
        assertThat($static->test(), isSameAs($static));
    }
    
    /**
     * @test
     * @dataProvider  classesWithStaticReturnTypes
     * @param  class-string  $class
     */
    public function stubReturnsItselfWhenReturnTypeHintIsStaticInDocCommentOnly(
        string $class
    ): void {
        /** @var ClassWithStaticReturnTypeHint&ClassProxy $static */
        $static = NewInstance::stub($class);
        assertThat($static->testWithDocComment(), isSameAs($static));
    }
}
