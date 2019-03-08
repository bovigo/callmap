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
 * Helper class used in tests.
 */
class Base7 implements OneMoreThing7
{
    /**
     * @return self
     */
    public function baz()
    {
        // no actual return on purpose
    }

    public function aha(): Base7
    {
        // no actual return on purpose
    }

    public function wow(): OneMoreThing7
    {
        // no actual return on purpose
    }
}