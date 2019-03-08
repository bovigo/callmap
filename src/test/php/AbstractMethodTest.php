<?php
declare(strict_types=1);
/**
 * This file is part of bovigo\callmap.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  bovigo_callmap
 */
namespace bovigo\callmap;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
/**
 * Helper class for the test.
 */
abstract class Instrument
{
    abstract public function play(int $roland = 303);
}
/**
 * Applies tests to a self defined class.
 */
class AbstractMethodTest extends TestCase
{
    /**
     * @type  bovigo\callmap\Proxy
     */
    private $proxy;

    /**
     * set up test environment
     */
    public function setUp(): void
    {
        $this->proxy = NewInstance::of(Instrument::class);
    }

    /**
     * @test
     */
    public function returnsNullIfMethodCallNotMapped()
    {
        assertThat($this->proxy->play(), isNull());
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall()
    {
        $this->proxy->returns(['play' => 'foo']);
        assertThat($this->proxy->play(), equals('foo'));
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall()
    {
        $this->proxy->returns(['play' => function() { return 'foo'; }]);
        assertThat($this->proxy->play(808), equals('foo'));
    }

    /**
     * @return  array
     */
    public function arguments(): array
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
        $this->proxy->returns(
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

        assertThat(
                null === $argument ? $this->proxy->play() : $this->proxy->play($argument),
                equals($expectedResult)
        );
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled()
    {
        verify($this->proxy, 'play')->wasNeverCalled();
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function recordsAmountOfCallsToMethod()
    {
        $this->proxy->play();
        $this->proxy->play(808);
        verify($this->proxy, 'play')->wasCalled(2);
    }

    /**
     * @test
     * @doesNotPerformAssertions
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
