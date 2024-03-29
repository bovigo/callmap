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

use Exception;

/**
 * Helper class for the test.
 */
class SomeClassWithMethodReturningVoid
{
    public function returnNothing(): void
    {
        // intentionally empty
    }

    public function returnNever(): never
    {
        throw new Exception('never returns');
    }
}