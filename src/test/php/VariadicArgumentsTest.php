<?php
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
 * Helper class for the test.
 */
class Variadic1
{
    public function reference(&...$foo)
    {
        // intentionally empty
    }

    public function something(...$foo)
    {
        // intentionally empty
    }

    public function doSomething($x, ...$foo)
    {
        // intentionally empty
    }

    public function other(self ...$bar)
    {
        // intentionally empty
    }

    public function otherThings($z, self ...$bar)
    {
        // intentionally empty
    }
}
/**
 * Tests for bovigo\callmap\NewInstance regarding variadic arguments.
 *
 * @group  variadic
 * @group  issue_9
 */
class VariadicArgumentsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function canCreateProxyForTypeWithVariadicArguments()
    {
        expect(function() { NewInstance::of(Variadic1::class); })
            ->doesNotThrow(\ReflectionException::class);
    }
}
