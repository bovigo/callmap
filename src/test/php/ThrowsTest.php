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
 * Test for bovigo\callmap\throws()
 *
 * @since  0.2.0
 */
class ThrowsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  bovigo\callmap\Proxy
     */
    private $proxy;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->proxy = NewInstance::of('\ReflectionObject', [$this]);
    }

    /**
     * @test
     * @expectedException  ReflectionException
     * @expectedExceptionMessage  some error
     */
    public function throwsExceptionPassedViaThrows()
    {
        $e = new \ReflectionException('some error');
        $this->proxy->mapCalls(['getName' => throws($e)]);
        $this->proxy->getName();
    }

    /**
     * @test
     * @expectedException  ReflectionException
     * @expectedExceptionMessage  some error
     */
    public function throwsExceptionPassedViaInvocationResults()
    {
        $e = new \ReflectionException('some error');
        $this->proxy->mapCalls(['getName' => new InvocationResults(['foo', throws($e)])]);
        $this->proxy->getName();
        $this->proxy->getName();
    }
}
