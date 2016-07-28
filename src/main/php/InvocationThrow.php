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
 * Container to pass exceptions to be throwed on a method invocation.
 *
 * @internal  Do not use directly, call bovigo\callmap\throws() instead.
 * @since  0.2.0
 */
class InvocationThrow
{
    /**
     * exception to be thrown on invocation of the method it has been assigned to
     * @type  \Throwable
     */
    private $e;

    /**
     * constructor
     *
     * @param  \Throwable  $e  exception to be thrown
     */
    public function __construct(\Throwable $e)
    {
        $this->e = $e;
    }

    /**
     * returns the exception to be thrown
     *
     * @return  \Throwable
     */
    public function exception(): \Throwable
    {
        return $this->e;
    }
}
