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
namespace bovigo\callmap\helper;
/**
 * Helper class for the test.
 *
 * @since 6.2.0
 */
class ClassWithStaticReturnTypeHint
{
    public function test(): static
    {
        return $this;
    }

    /**
     * @return  static
     */
    public function testWithDocComment()
    {
        return $this;
    }
}
