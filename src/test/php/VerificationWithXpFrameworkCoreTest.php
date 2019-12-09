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
    /**
     * @var  Verification
     */
    private $xpFrameworkCoreVerification;

    public function setUp(): void
    {
        $this->xpFrameworkCoreVerification = new class(new Invocations('', [])) extends Verification
        {
            public function evaluateWithXpFrameworkCore($constraint, $received, string $description): bool
            {
                return parent::evaluateWithXpFrameworkCore($constraint, $received, $description);
            }
        };
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function throwsAssertionFailedErrorWhenWhenBothValueAreNotEqual(): void
    {
        expect(function() {
            $this->xpFrameworkCoreVerification->evaluateWithXpFrameworkCore(
                'foo',
                'bar',
                'some description'
            );
        })
            ->throws(\unittest\AssertionFailedError::class)
            ->message(contains('some description'));
    }
}
