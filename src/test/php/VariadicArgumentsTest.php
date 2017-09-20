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
use PHPUnit\Framework\TestCase;

use function bovigo\assert\expect;
/**
 * Helper class for the test.
 */
class Variadic1
{
    public function something(...$foo)
    {
        // intentionally empty
    }

    public function doSomething($x, ...$foo)
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
class VariadicArgumentsTest extends TestCase
{
    /**
     * @test
     */
    public function canCreateProxyForTypeWithVariadicArguments()
    {
        expect(function() { NewInstance::of(Variadic1::class); })
            ->doesNotThrow(\ReflectionException::class);
    }

    /**
     * @test
     */
    public function canCreateProxyForTypeWithVariadicReference()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM does not support variadic parameters by reference');
        }

        eval(
                'class VariadicReference
                {
                    public function reference(&...$foo)
                    {
                        // intentionally empty
                    }
                }'
        );

        expect(function() { NewInstance::of(VariadicReference::class); })
            ->doesNotThrow(\ReflectionException::class);
    }

    /**
     * @test
     */
    public function canCreateProxyForTypeWithVariadicTypehint()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM does not support variadic parameters with type hints');
        }

        eval(
                'class VariadicTypeHint
                {
                    public function other(self ...$bar)
                    {
                        // intentionally empty
                    }

                    public function otherThings($z, self ...$bar)
                    {
                        // intentionally empty
                    }
                }'
        );

        expect(function() { NewInstance::of(VariadicTypeHint::class); })
            ->doesNotThrow(\ReflectionException::class);
    }
}
