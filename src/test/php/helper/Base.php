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
 * Helper class for the test below.
 */
class Base implements OneMoreThing
{
    /**
     * @return self
     */
    public function baz()
    {
        // no actual return on purpose
    }

    /**
     * @return  Base
     */
    public function aha()
    {
        // no actual return on purpose
    }

    /**
     * @return  OneMoreThing
     */
    public function wow()
    {
        // no actual return on purpose
    }
}