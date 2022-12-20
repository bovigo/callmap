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
 * @since 7.0.0
 */
class ClassWithIntersectionAndUnionTypeHints
{
    public function mayAccept(null|(A&B) $acceptable): void
    {
        // intentionally empty
    }

    public function mayReturn(): null|(A&B)
    {
        return null;
    }
}
/**
 * Helper function.
 *
 * @since 7.0.0
 */
function exampleFunctionWithIntersectionAndUnionTypeHints(null|(A&B) $extra): null|(A&B)
{
    return $extra;
}
