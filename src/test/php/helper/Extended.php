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

use ReturnTypeWillChange;

/**
 * Helper class for the test below.
 */
class Extended extends Base implements Bar, \IteratorAggregate
{
    /**
     * @since 8.0.4
     */
    public function nonoptionalnullable(?int $foofoofoo): void
    {
        // intentionally empty
    }

    /**
     * @since 8.0.4
     */
    public function optionalnullable(int $bambambam = null): void
    {
        // intentionally empty
    }

    /**
     * @since 8.0.4
     */
    public function optionalnullable2(?int $bambambam = null): void
    {
        // intentionally empty
    }

    /**
     * @return  Bar
     */
    public function foo()
    {
        // no actual return on purpose
    }

    /**
     * @return $this
     */
    public function yo()
    {

    }

    /**
     * @return  \bovigo\callmap\helper\Extended
     */
    public function action()
    {
        // no actual return on purpose
    }

    /**
     * @return  \bovigo\callmap\helper\Extended
     */
    public function moreAction()
    {
        // no actual return on purpose
    }

    /**
     * @return  Extended
     */
    public function minorAction()
    {
        // no actual return on purpose
    }

    /**
     * @return  \Traversable
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        // no actual return on purpose
    }

    /**
     * @return Other
     */
    public function other()
    {
        // no actual return on purpose
    }
}