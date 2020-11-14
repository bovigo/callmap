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
class ClassWithMixedTypeHints
{
    /**
     * Method with with mixed parameter type hint.
     *
     * @param  mixed  $foo
     */
    public function accept(mixed $something): void
    {
        // intentionally empty
    }

    /**
     * Method with mixed return type hint.
     *
     * @return  mixed
     */
    public function doReturn(): mixed
    {
        return 303;
    }
}

/**
 * Function with mixed parameter type hint.
 *
 * @param  mixed  $something
 */
function acceptMixed(mixed $something): void
{
    // intentionally empty
}

/**
 * Function with mixed return type hint.
 *
 * @return  mixed
 */
function returnMixed(): mixed
{
    return 303;
}
