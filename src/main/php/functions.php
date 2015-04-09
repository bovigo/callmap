<?php
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap;
/**
 * creates a closure which throws the given exception
 *
 * @param   \Exception  $e
 * @return  \Closure
 * @since   0.2.0
 */
function throws(\Exception $e)
{
    return function() use ($e) { throw $e; };
}
