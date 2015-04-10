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
 * Tests for call mapping with a list of return values.
 */
class InvocationResultsTest extends \PHPUnit_Framework_TestCase
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
     */
    public function mapToInvocationResultsReturnsResultOnMethodCall()
    {
        $this->proxy->mapCalls(
            ['getName' => onConsecutiveCalls('foo', 'bar', 'baz')]
        );
        foreach (['foo', 'bar', 'baz'] as $expected) {
            assertEquals($expected, $this->proxy->getName());
        }
    }

    /**
     * @test
     */
    public function invocationResultIsNullIfCalledMoreOftenThenResultsDefined()
    {
        $this->proxy->mapCalls(['getName' => onConsecutiveCalls('foo')]);
        $this->proxy->getName(); // foo
        assertNull($this->proxy->getName());
    }
}
