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
 * Container to pass exceptions to be throwed on a method invocation.
 *
 * @internal
 * @since  0.2.0
 */
class Throwable
{
    /**
     * @type  \Exception
     */
    private $e;

    /**
     * constructor
     *
     * @param  \Exception  $e
     */
    public function __construct(\Exception $e)
    {
        $this->e = $e;
    }

    /**
     * returns the exception to be thrown
     *
     * @return  \Exception
     */
    public function exception()
    {
        return $this->e;
    }
}

