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

use bovigo\callmap\helper\Instrument;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNull;
/**
 * Applies tests to a self defined class.
 */
class AbstractMethodTest extends TestCase
{
    private Instrument&ClassProxy $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::of(Instrument::class);
    }

    #[Test]
    public function returnsNullIfMethodCallNotMapped(): void
    {
        assertThat($this->proxy->play(), isNull());
    }

    #[Test]
    public function mapToSimpleValueReturnsValueOnMethodCall(): void
    {
        $this->proxy->returns(['play' => 'foo']);
        assertThat($this->proxy->play(), equals('foo'));
    }

    #[Test]
    public function mapToClosureReturnsClosureReturnValueOnMethodCall(): void
    {
        $this->proxy->returns(['play' => function() { return 'foo'; }]);
        assertThat($this->proxy->play(808), equals('foo'));
    }

    public static function arguments(): Generator
    {
        yield [null, 'blubber'];
        yield [808, 'ba-dummz!'];
        yield [909, 'foo'];
    }

    /**
     * @since 0.2.0
     */
    #[Test]
    #[DataProvider('arguments')]
    public function givenArgumentsArePassedToClosure(
        ?int $argument,
        string $expectedResult
    ): void {
        $this->proxy->returns([
            'play' => function(int $roland = 303)
                      {
                          if (303 === $roland) {
                              return 'blubber';
                          } elseif (808 === $roland) {
                              return 'ba-dummz!';
                          }

                          return 'foo';
                      }
        ]);

        assertThat(
            null === $argument ? $this->proxy->play() : $this->proxy->play($argument),
            equals($expectedResult)
        );
    }

    #[Test]
    public function amountOfCallsToMethodIsZeroIfNotCalled(): void
    {
        verify($this->proxy, 'play')->wasNeverCalled();
    }

    #[Test]
    public function recordsAmountOfCallsToMethod(): void
    {
        $this->proxy->play();
        $this->proxy->play(808);
        verify($this->proxy, 'play')->wasCalled(2);
    }

    #[Test]
    public function optionalArgumentsCanNotBeVerifiedWhenNotExplicitlyPassed(): void
    {
        $this->proxy->play();
        verify($this->proxy, 'play')->receivedNothing();
    }

    #[Test]
    public function canVerifyReceivedArguments(): void
    {
        $this->proxy->play(808);
        verify($this->proxy, 'play')->received(808);
    }

    #[Test]
    public function canVerifyReceivedArgumentsOfSpecificInvocation(): void
    {
        $this->proxy->play(808);
        $this->proxy->play(909);
        verify($this->proxy, 'play')->receivedOn(2, 909);
    }
}
