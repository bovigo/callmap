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
 * Helper class for the test.
 */
class SelfDefined
{
    public function action(self $self, callable $something, array $optional = [], $roland = 303): string
    {
        return 'selfdefined';
    }

    public function passByReference(&$foo, array $bar = ['baz' => 303])
    {

    }

    public function optionalNull($baz = null)
    {

    }
}