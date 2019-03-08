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
use PHPUnit\Framework\TestCase;

use function bovigo\assert\expect;
use function bovigo\callmap\helper\whichReturnsNothing;
/**
 * Tests for functions which are declared with return type void.
 *
 * @since  4.0.1
 * @group  void
 */
class ReturnVoidTest extends TestCase
{
    /**
     * @test
     */
    public function voidRequiresNoReturnForMethods()
    {
        expect(function() {
            NewInstance::of(SomeClassWithMethodReturningVoid::class)->returnNothing();
        })->doesNotThrow();
    }

    /**
     * @test
     */
    public function voidRequiresNoReturnForFunctions()
    {
        expect(function() {
            NewCallable::of('bovigo\callmap\helper\whichReturnsNothing')();
        })->doesNotThrow();
    }

    /**
     * @test
     */
    public function mapVoidMethodFails()
    {
        expect(function() {
            NewInstance::of(SomeClassWithMethodReturningVoid::class)->returns([
                'returnNothing' => true
            ]);
        })->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to map method ' . SomeClassWithMethodReturningVoid::class . '::returnNothing(), but it is declared as returning void.');
    }

    /**
     * @test
     */
    public function mapVoidFunctionFails()
    {
        expect(function() {
            NewCallable::of('bovigo\callmap\helper\whichReturnsNothing')->returns(true);
        })->throws(\LogicException::class)
        ->withMessage('Trying to map function bovigo\callmap\helper\whichReturnsNothing(), but it is declared as returning void.');
    }
}
