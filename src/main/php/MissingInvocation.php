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
     * Thrown when it is attempted to verify parameters of an invocation that never happened.
     *
     * @since  0.5.0
     */
    class MissingInvocation extends \PHPUnit_Framework_ExpectationFailedException
    {
        // intentionally empty
    }
} else {
    /**
     * @ignore
     */
    class MissingInvocation extends \Exception
    {
        // intentionally empty
    }
}

