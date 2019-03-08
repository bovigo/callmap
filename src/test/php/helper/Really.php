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
 * @since  3.0.2
 */
class Really implements WithSelfReturnTypeHint
{
    public function wow(): WithSelfReturnTypeHint { return $this; }

    public function hui(): self { return $this; }
}