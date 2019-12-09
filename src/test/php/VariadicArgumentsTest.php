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
use bovigo\callmap\helper\Variadic1;
use bovigo\callmap\helper\VariadicReference;
use bovigo\callmap\helper\VariadicTypeHint;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\expect;
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
    public function canCreateProxyForTypeWithVariadicArguments(): void
    {
        expect(function() { NewInstance::of(Variadic1::class); })
            ->doesNotThrow(\ReflectionException::class);
    }

    /**
     * @test
     */
    public function canCreateProxyForTypeWithVariadicReference(): void
    {
        expect(function() { NewInstance::of(VariadicReference::class); })
            ->doesNotThrow(\ReflectionException::class);
    }

    /**
     * @test
     */
    public function canCreateProxyForTypeWithVariadicTypehint(): void
    {
        expect(function() { NewInstance::of(VariadicTypeHint::class); })
            ->doesNotThrow(\ReflectionException::class);
    }
}
