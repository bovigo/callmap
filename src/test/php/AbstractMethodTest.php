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
     * @return  array
     */
    public function arguments()
    {
        return [[null, 'blubber'], [808, 'ba-dummz!'], [909, 'foo']];
    }

    /**
     * @test
     * @dataProvider  arguments
     * @since  0.2.0
     */
    public function givenArgumentsArePassedToClosure($argument, $expectedResult)
    {
        $this->proxy->mapCalls(
                ['play' => function($roland = 303)
                            {
                                if (303 === $roland) {
                                    return 'blubber';
                                } elseif (808 === $roland) {
                                    return 'ba-dummz!';
                                }

                                return 'foo';
                            }
                ]
        );

        assertEquals(
                $expectedResult,
                null === $argument ? $this->proxy->play() : $this->proxy->play($argument)
        );
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        assertTrue(verify($this->proxy, 'play')->wasNeverCalled());
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->play();
        $this->proxy->play(808);
        assertTrue(verify($this->proxy, 'play')->wasCalled(2));
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullIfMethodNotCalled()
    {
        assertNull($this->proxy->argumentsReceivedFor('play'));
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsDoesNotContainOptionalArguments()
    {
        $this->proxy->play();
        assertEquals(
                [],
                $this->proxy->argumentsReceivedFor('play')
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
                $this->proxy->argumentsReceivedFor('play')
        );
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsIsNullWhenNotCalledForRequestedInvocationCount()
    {
        $this->proxy->play(808);
        assertNull($this->proxy->argumentsReceivedFor('play', 2));
    }
}
