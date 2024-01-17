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

use PDOStatement;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XSLTProcessor;

use function PHPUnit\Framework\assertInstanceOf;

/**
 * @since 8.0.4
 */
#[Group('bug_nonoptional_nullable')]
class NonOptionalNullableTest extends TestCase
{
    /**
     * XSLTProcessor::setProfiling(?string $filename) has
     * a non-optional nullable parameter.
     */
    #[Test]
    #[Group('xsl')]
    #[RequiresPhpExtension('xsl')]
    public function canCreateClassProxy(): void
    {
        assertInstanceOf(
            ClassProxy::class,
            NewInstance::of(XSLTProcessor::class)
        );
    }

    /**
     * @since 8.0.6
     */
    #[Test]
    #[Group('pdo')]
    #[RequiresPhpExtension('pdo')]
    public function canCreatePdoStatementProxy(): void
    {
        assertInstanceOf(
            ClassProxy::class,
            NewInstance::of(PDOStatement::class)
        );
    }
}
