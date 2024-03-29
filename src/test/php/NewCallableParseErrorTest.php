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

use ParseError;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\expect;
/**
 * Tests for bovigo\callmap\NewCallable regarding parse errors in eval().
 *
 * @since  3.1.0
 */
#[Group('eval')]
class NewCallableParseErrorTest extends TestCase
{
    protected function setUp(): void
    {
        NewCallable::$compile = fn() => throw new ParseError('failed to evaluate');
    }

    protected function tearDown(): void
    {
        NewCallable::$compile = __NAMESPACE__ . '\compile';
    }

    #[Test]
    public function throwsProxyCreationFailureWhenEvalOfCreatedProxyClassFails(): void
    {
        expect(fn() => NewCallable::of('strlen'))
            ->throws(ProxyCreationFailure::class)
            ->withMessage(
                'Failure while creating callable CallMap instance of '
                . 'strlen(): failed to evaluate'
            );
    }
}
