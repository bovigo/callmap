<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap\verification;
if (class_exists('PHPUnit\Framework\AssertionFailedError')) {
    /**
     * Thrown when amount of received arguments is lower than expected amount.
     *
     * @since  0.5.0
     */
    class ArgumentMismatch extends \PHPUnit\Framework\AssertionFailedError
    {
        // intentionally empty
    }
} else {
    /**
     * @ignore
     */
    class ArgumentMismatch extends \Exception
    {
        // intentionally empty
    }
}
