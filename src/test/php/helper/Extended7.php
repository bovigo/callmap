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
class Extended7 extends Base7 implements Bar7, \IteratorAggregate
{
    public function foo(): Bar7
    {
        // no actual return on purpose
    }

    /**
     * @return $this
     */
    public function yo()
    {

    }

    public function action(): \bovigo\callmap\helper\Extended7
    {
        // no actual return on purpose
    }

    public function moreAction(): \bovigo\callmap\helper\Extended7
    {
        // no actual return on purpose
    }

    public function minorAction(): \bovigo\callmap\helper\Extended7
    {
        // no actual return on purpose
    }

    /**
     * @return  \Traversable
     */
    public function getIterator()
    {
        // no actual return on purpose
    }

    /**
     * @return  Other
     */
    public function other()
    {
        // no actual return on purpose
    }
}