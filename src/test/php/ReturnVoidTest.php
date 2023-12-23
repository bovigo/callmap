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

use bovigo\callmap\helper\SomeClassWithMethodReturningVoid;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\expect;
/**
 * Tests for functions which are declared with return type void.
 *
 * @since 4.0.1
 */
#[Group('void')]
class ReturnVoidTest extends TestCase
{
    #[Test]
    public function voidRequiresNoReturnForMethods(): void
    {
        expect(fn() =>
            NewInstance::of(SomeClassWithMethodReturningVoid::class)->returnNothing()
        )->doesNotThrow();
    }

    #[Test]
    public function voidRequiresNoReturnForFunctions(): void
    {
        expect(fn() =>
            NewCallable::of('bovigo\callmap\helper\whichReturnsNothing')()
        )->doesNotThrow();
    }

    #[Test]
    public function mapVoidMethodFails(): void
    {
        expect(fn() =>
            NewInstance::of(SomeClassWithMethodReturningVoid::class)->returns([
                'returnNothing' => true
            ])
        )
            ->throws(\InvalidArgumentException::class)
            ->withMessage(
                'Trying to map method '
                . SomeClassWithMethodReturningVoid::class
                . '::returnNothing(), but it is declared as returning void.'
            );
    }

    #[Test]
    public function mapVoidFunctionFails(): void
    {
        expect(fn() =>
            NewCallable::of('bovigo\callmap\helper\whichReturnsNothing')->returns(true)
        )
            ->throws(\LogicException::class)
            ->withMessage('Trying to map function bovigo\callmap\helper\whichReturnsNothing(), but it is declared as returning void.');
    }
}
