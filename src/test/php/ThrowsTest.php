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

use function bovigo\assert\expect;
use function bovigo\assert\predicate\contains;
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
        $this->proxy = NewInstance::of(\ReflectionObject::class, [$this]);
    }

    /**
     * @test
     */
    public function throwsExceptionPassedViaThrows()
    {
        expect(function() {
            $e = new \ReflectionException('some error');
            $this->proxy->mapCalls(['getName' => throws($e)]);
            $this->proxy->getName();
        })
        ->throws(\ReflectionException::class)
        ->message(contains('some error'));

    }

    /**
     * @test
     */
    public function throwsExceptionPassedViaInvocationResults()
    {
        expect(function() {
                $e = new \ReflectionException('some error');
                $this->proxy->mapCalls(
                        ['getName' => onConsecutiveCalls('foo', throws($e))]
                );
                $this->proxy->getName(); // foo
                $this->proxy->getName(); // throws $e
        })
        ->throws(\ReflectionException::class)
        ->message(contains('some error'));
    }
}
