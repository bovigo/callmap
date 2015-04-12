<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
if (class_exists('PHPUnit_Framework_ExpectationFailedException')) {
    /**
     * Thrown when amount of received arguments is lower than expected amount.
     *
     * @since  0.5.0
     */
    class ArgumentMismatch extends \PHPUnit_Framework_ExpectationFailedException
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

