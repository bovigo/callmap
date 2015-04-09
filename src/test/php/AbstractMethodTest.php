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
 * Helper class for the test.
 */
abstract class Instrument
{
    abstract public function play($roland = 303);
}
/**
 * Applies tests to a self defined class.
 */
class AbstractMethodTest extends \PHPUnit_Framework_TestCase
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
        $this->proxy = NewInstance::of('bovigo\callmap\Instrument');
    }

    /**
     * @test
     */
    public function returnsNullIfMethodCallNotMapped()
    {
        assertNull($this->proxy->play());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->mapCalls(['play' => 'foo']);
        assertEquals(
                'foo',
                $this->proxy->play()
        );
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['play' => function() { return 'foo'; }]);
        assertEquals(
                'foo',
                $this->proxy->play(808)
        );
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        assertEquals(0, $this->proxy->callsReceivedFor('play'));
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->play();
        $this->proxy->play(808);
        assertEquals(2, $this->proxy->callsReceivedFor('play'));
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullIfMethodNotCalled()
    {
        assertNull($this->proxy->argumentsReceived('play'));
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsDoesNotContainOptionalArguments()
    {
        $this->proxy->play();
        assertEquals(
                [],
                $this->proxy->argumentsReceived('play')
        );
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsContainsGivenArguments()
    {
        $this->proxy->play(808);
        assertEquals(
                [808],
                $this->proxy->argumentsReceived('play')
        );
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullWhenNotCalledForRequestedInvocationCount()
    {
        $this->proxy->play(808);
        assertNull($this->proxy->argumentsReceived('play', 2));
    }
}
