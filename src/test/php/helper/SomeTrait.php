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
 * Helper trait for the test.
 */
trait SomeTrait
{
    public function action($something)
    {
        return $something;
    }

    abstract public function other(array $optional = [], int $roland = 303);
}