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
 * Tests for bovigo\callmap\NewCallable regarding parse errors in eval().
 *
 * @since  3.1.0
 * @group  eval
 */
class NewCallableParseErrorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        NewCallable::$compile = NewCallable::of(__NAMESPACE__ . '\compile')
                ->mapCall(throws(new \ParseError('failed to evaluate')));
    }

    public function tearDown()
    {
        NewCallable::$compile = __NAMESPACE__ . '\compile';
    }

    /**
     * @test
     */
    public function throwsProxyCreationFailureWhenEvalOfCreatedProxyClassFails()
    {
        expect(function() { NewCallable::of('strlen'); })
                ->throws(ProxyCreationFailure::class)
                ->withMessage(
                        'Failure while creating callable CallMap instance of '
                        . 'strlen(): failed to evaluate'
                );
    }
}
