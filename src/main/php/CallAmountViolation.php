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
     * Exception to be thrown when the call amount of a method is not the expected amount of times.
     *
     * @since  0.5.0
     */
    class CallAmountViolation extends \PHPUnit_Framework_ExpectationFailedException
    {
        // intentionally empty
    }
} else {
    /**
     * @ignore
     */
    class CallAmountViolation extends \Exception
    {
        // intentionally empty
    }
}

