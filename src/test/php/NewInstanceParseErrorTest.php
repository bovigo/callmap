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
use bovigo\callmap\helper\FailingTrait;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\expect;
/**
 * Tests for bovigo\callmap\NewInstance regarding parse errors in eval().
 *
 * @since  3.0.0
 * @group  eval
 */
class NewInstanceParseErrorTest extends TestCase
{
    public function setUp(): void
    {
        NewInstance::$compile = NewCallable::of(__NAMESPACE__ . '\compile')
                ->throws(new \ParseError('failed to evaluate'));
    }

    public function tearDown(): void
    {
        NewInstance::$compile = __NAMESPACE__ . '\compile';
    }

    /**
     * @test
     */
    public function throwsProxyCreationFailureWhenEvalOfCreatedProxyClassFails(): void
    {
        expect(function() { NewInstance::of(__CLASS__); })
            ->throws(ProxyCreationFailure::class)
            ->withMessage(
                'Failure while creating CallMap instance of '
                . __CLASS__ . ': failed to evaluate'
            );
    }

    /**
     * @test
     */
    public function throwsProxyCreationFailureWhenEvalOfCreatedProxyTraitFails(): void
    {
        expect(function() { NewInstance::of(FailingTrait::class); })
            ->throws(ProxyCreationFailure::class)
            ->withMessage(
                'Failure while creating forked trait instance of '
                . FailingTrait::class . ': failed to evaluate'
            );
    }
}
