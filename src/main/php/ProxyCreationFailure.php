<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace bovigo\callmap;
/**
 * Exception when creation of proxy for a class fails.
 *
 * @since  3.0.0
 */
class ProxyCreationFailure extends \Exception
{
    public function __construct(string $message, \Throwable $cause)
    {
        parent::__construct($message, $cause->getCode(), $cause);
    }
}
