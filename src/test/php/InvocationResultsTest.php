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
        $returnValues = ['foo', 'bar', 'baz'];
        $this->proxy->mapCalls(
            ['getName' => new InvocationResults($returnValues)]
        );
        foreach ($returnValues as $expected) {
            assertEquals($expected, $this->proxy->getName());
        }
    }

    /**
     * @test
     */
    public function invocationResultIsNullIfCalledMoreOftenThenResultsDefined()
    {
        $this->proxy->mapCalls(['getName' => new InvocationResults([])]);
        assertNull($this->proxy->getName());
    }

    /**
     * helper method for callable test invocationResultsCallsCallable()
     *
     * @return  string
     */
    public static function foo()
    {
        return 'foo';
    }

    /**
     * @return  array
     */
    public function callables()
    {
        return [[function() { return 'foo';}], [[__CLASS__, 'foo']]];
    }

    /**
     * @param  callable  $callable
     * @test
     * @dataProvider  callables
     * @since  0.2.0
     */
    public function invocationResultsCallsCallable(callable $callable)
    {
        $this->proxy->mapCalls(
                ['getName' => new InvocationResults([$callable])]
        );
        assertEquals('foo', $this->proxy->getName());
    }
}
