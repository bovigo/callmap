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

use bovigo\callmap\internal\Invocations;
use \bovigo\callmap\verification\Verification;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use unittest\AssertionFailedError;

use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\contains;
 /**
  * Test for bovigo\callmap\verify() using xp-framework/core unittest.
  *
  * @since  3.0.0
  */
class VerificationWithXpFrameworkCoreTest extends TestCase
{
    private Verification $xpFrameworkCoreVerification;

    protected function setUp(): void
    {
        $this->xpFrameworkCoreVerification = new class(new Invocations('', [])) extends Verification
        {
            public function evaluateWithXpFrameworkCore(
                mixed $constraint,
                mixed $received,
                string $description
            ): bool {
                return parent::evaluateWithXpFrameworkCore(
                    $constraint,
                    $received,
                    $description
                );
            }
        };
    }

    #[Test]
    public function returnsTrueWhenBothValueAreEqual(): void
    {
        assertTrue(
            $this->xpFrameworkCoreVerification->evaluateWithXpFrameworkCore(
                'foo',
                'foo',
                ''
            )
        );
    }

    #[Test]
    public function throwsAssertionFailedErrorWhenWhenBothValueAreNotEqual(): void
    {
        expect(fn() =>
            $this->xpFrameworkCoreVerification->evaluateWithXpFrameworkCore(
                'foo',
                'bar',
                'some description'
            )
        )
            ->throws(AssertionFailedError::class)
            ->message(contains('some description'));
    }
}
