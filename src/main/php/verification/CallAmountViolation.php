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
     * Exception to be thrown when the call amount of a method is not the expected amount of times.
     *
     * @since  0.5.0
     */
    class CallAmountViolation extends \PHPUnit\Framework\AssertionFailedError
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
