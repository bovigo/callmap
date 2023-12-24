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
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertTrue;
 /**
  * Test for bovigo\callmap\verify() using PHPUnit constraints.
  *
  * @since 3.0.0
  */
class VerificationWithPhpUnitTest extends TestCase
{
    private Verification $phpUnitVerification;

    protected function setUp(): void
    {
        $this->phpUnitVerification = new class(new Invocations('', [])) extends Verification
        {
            public function evaluateWithPhpUnit(
                mixed $constraint,
                mixed $received,
                string $description
            ): bool {
                return parent::evaluateWithPhpUnit($constraint, $received, $description);
            }
        };
    }

    #[Test]
    public function usingNoConstraintFallsBackToIsEquals(): void
    {
        assertTrue($this->phpUnitVerification->evaluateWithPhpUnit('foo', 'foo', ''));
    }

    #[Test]
    public function usingConstraintEvaluatesWithThisConstraint(): void
    {
        $constraint = NewInstance::of(Constraint::class)
                ->returns(['evaluate' => true]);
        assertTrue($this->phpUnitVerification->evaluateWithPhpUnit(
            $constraint,
            'foo',
            'some description'
        ));
        verify($constraint, 'evaluate')->received('foo', 'some description');
    }
}
