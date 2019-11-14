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
 * One more helper, this time with a PHP 7 return type hint.
 *
 * @since  2.0.0
 */
class ReturnTypeHints
{
    public function something(): array
    {
        return [];
    }

    public function fump(): ?Fump
    {
        return null;
    }

    public function returnsVoid(): void
    {
        // intentionally empty
    }
}