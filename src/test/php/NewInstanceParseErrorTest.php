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
use function bovigo\assert\expect;
/**
 * Helper trait for the test.
 */
trait FailingTrait
{
    public function action($something)
    {
        return $something;
    }
}
/**
 * overwrite global compile function to be able to simulate parse errors
 *
 * @since  3.0.0
 */
function compile(string $code)
{
    if (NewInstanceParseErrorTest::$failCompile) {
        throw new \ParseError('failed to evaluate');
    }

    return \compile($code);
}
/**
 * Tests for bovigo\callmap\NewInstance regarding parse errors in eval().
 *
 * @since  3.0.0
 * @group  eval
 */
class NewInstanceParseErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  bool
     */
    public static $failCompile = false;

    public function setUp()
    {
        self::$failCompile = true;
    }

    public function tearDown()
    {
        self::$failCompile = false;
    }

    /**
     * @test
     */
    public function throwsProxyCreationFailureWhenEvalOfCreatedProxyClassFails()
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
    public function throwsProxyCreationFailureWhenEvalOfCreatedProxyTraitFails()
    {
        expect(function() { NewInstance::of(FailingTrait::class); })
                ->throws(ProxyCreationFailure::class)
                ->withMessage(
                        'Failure while creating forked trait instance of '
                        . FailingTrait::class . ': failed to evaluate'
                );
    }
}
