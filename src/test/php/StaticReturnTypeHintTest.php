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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\{
    assertThat,
    predicate\isSameAs
};
/**
 * Tests for methods which are declared with return type static.
 *
 * @since 6.2.0
 */
#[Group('typehint')]
#[Group('static')]
class StaticReturnTypeHintTest extends TestCase
{
    public static function classesWithStaticReturnTypes(): Generator
    {
        yield [ClassWithStaticReturnTypeHint::class];
        yield [ExtendedClassWithStaticReturnTypeHint::class];
    }

    /**
     * @param  class-string  $class
     */
    #[Test]
    #[DataProvider('classesWithStaticReturnTypes')]
    public function stubReturnsItselfWhenReturnTypeHintIsStatic(string $class): void
    {
        /** @var ClassWithStaticReturnTypeHint&ClassProxy $static */
        $static = NewInstance::stub($class);
        assertThat($static->test(), isSameAs($static));
    }
    
    /**
     * @param  class-string  $class
     */
    #[Test]
    #[DataProvider('classesWithStaticReturnTypes')]
    public function stubReturnsItselfWhenReturnTypeHintIsStaticInDocCommentOnly(
        string $class
    ): void {
        /** @var ClassWithStaticReturnTypeHint&ClassProxy $static */
        $static = NewInstance::stub($class);
        assertThat($static->testWithDocComment(), isSameAs($static));
    }
}
