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
use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
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
        assert($this->proxy->play(), isNull());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->mapCalls(['play' => 'foo']);
        assert($this->proxy->play(), equals('foo'));
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->mapCalls(['play' => function() { return 'foo'; }]);
        assert($this->proxy->play(808), equals('foo'));
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

        assert(
                null === $argument ? $this->proxy->play() : $this->proxy->play($argument),
                equals($expectedResult)  
        );
    }

    /**
     * @test
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        verify($this->proxy, 'play')->wasNeverCalled();
    }

    /**
     * @test
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->play();
        $this->proxy->play(808);
        verify($this->proxy, 'play')->wasCalled(2);
    }

    /**
     * @test
     */
    public function optionalArgumentsCanNotBeVerifiedWhenNotExplicitlyPassed()
    {
        $this->proxy->play();
        verify($this->proxy, 'play')->receivedNothing();
    }

    /**
     * @test
     */
    public function canVerifyReceivedArguments()
    {
        $this->proxy->play(808);
        verify($this->proxy, 'play')->received(808);
    }

    /**
     * @test
     */
    public function canVerifyReceivedArgumentsOfSpecificInvocation()
    {
        $this->proxy->play(808);
        $this->proxy->play(909);
        verify($this->proxy, 'play')->receivedOn(2, 909);
    }
}
