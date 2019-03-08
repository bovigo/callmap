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
 /**
  * Test for bovigo\callmap\verify() using PHPUnit constraints.
  *
  * @since  3.0.0
  */
class VerificationWithPhpUnitTest extends TestCase
{
    private $phpUnitVerification;

    public function setUp(): void
    {
        $this->phpUnitVerification = new class(new Invocations('', [])) extends Verification
        {
            public function evaluateWithPhpUnit($constraint, $received, string $description): bool
            {
                return parent::evaluateWithPhpUnit($constraint, $received, $description);
            }
        };
    }

    /**
     * @test
     */
    public function usingNoConstraintFallsBackToIsEquals()
    {
        assertTrue($this->phpUnitVerification->evaluateWithPhpUnit('foo', 'foo', ''));
    }

    /**
     * @test
     */
    public function usingConstraintEvaluatesWithThisConstraint()
    {
        $constraint = NewInstance::of(\PHPUnit\Framework\Constraint\Constraint::class)
                ->mapCalls(['evaluate' => true]);
        assertTrue($this->phpUnitVerification->evaluateWithPhpUnit(
                $constraint,
                'foo',
                'some description'
        ));
        verify($constraint, 'evaluate')->received('foo', 'some description');
    }
}
