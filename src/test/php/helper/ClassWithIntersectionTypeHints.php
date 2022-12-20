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
 * Helper interface.
 *
 * @since 7.0.0
 */
interface A
{
    public function accept(A&B $acceptable): void;
}
/**
 * Helper interface.
 *
 * @since 7.0.0
 */
interface B
{
    public function doReturn(): A&B;
}
/**
 * Helper class for the test.
 *
 * @since 7.0.0
 */
class ClassWithIntersectionTypeHints implements A, B
{
    public function accept(A&B $acceptable): void
    {
        // intentionally empty
    }

    public function doReturn(): A&B
    {
        return $this;
    }
}
/**
 * Helper function.
 *
 * @since 7.0.0
 */
function exampleFunctionWithIntersectionTypeHints(A&B $extra): A&B
{
    return $extra;
}
