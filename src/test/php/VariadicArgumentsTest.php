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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\expect;
/**
 * Tests for bovigo\callmap\NewInstance regarding variadic arguments.
 */
#[Group('variadic')]
#[Group('issue_9')]
class VariadicArgumentsTest extends TestCase
{
    #[Test]
    public function canCreateProxyForTypeWithVariadicArguments(): void
    {
        expect(fn() => NewInstance::of(Variadic1::class))
            ->doesNotThrow(\ReflectionException::class);
    }

    #[Test]
    public function canCreateProxyForTypeWithVariadicReference(): void
    {
        expect(fn() => NewInstance::of(VariadicReference::class))
            ->doesNotThrow(\ReflectionException::class);
    }

    #[Test]
    public function canCreateProxyForTypeWithVariadicTypehint(): void
    {
        expect(fn() => NewInstance::of(VariadicTypeHint::class))
            ->doesNotThrow(\ReflectionException::class);
    }
}
