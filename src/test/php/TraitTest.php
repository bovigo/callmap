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
use bovigo\callmap\helper\SomeTrait;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\each;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isOfType;
/**
 * Applies tests to a self defined class.
 *
 * @group  issue_1
 */
class TraitTest extends TestCase
{
    /**
     * @var  \bovigo\callmap\ClassProxy
     */
    private $proxy;

    public function setUp(): void
    {
        $this->proxy = NewInstance::of(SomeTrait::class);
    }

    /**
     * @test
     */
    public function callsOriginalMethodIfNoMappingProvided(): void
    {
        assertThat($this->proxy->action(313), equals(313));
    }

    /**
     * @test
     */
    public function mapToSimpleValueReturnsValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => 'foo']);
        assertThat($this->proxy->action(313), equals('foo'));
    }

    /**
     * @test
     */
    public function mapToClosureReturnsClosureReturnValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => function() { return 'foo'; }]);
        assertThat($this->proxy->action(313), equals('foo'));
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function amountOfCallsToMethodIsZeroIfNotCalled(): void
    {
        verify($this->proxy, 'action')->wasNeverCalled();
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function recordsAmountOfCallsToMethod(): void
    {
        $this->proxy->action(303);
        $this->proxy->action(313);
        verify($this->proxy, 'action')->wasCalled(2);
    }

    /**
     * @test
     */
    public function returnsListOfReceivedArgumentsIfMethodCalled(): void
    {
        $this->proxy->action(313);
        verify($this->proxy, 'action')->received(313);
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function optionalArgumentsCanNotBeVerifiedWhenNotExplicitlyPassed(): void
    {
        $this->proxy->other();
        verify($this->proxy, 'other')->receivedNothing();
    }

    /**
     * @test
     */
    public function listOfReceivedArgumentsContainsGivenArguments(): void
    {
        $this->proxy->other(['play' => 808]);
        verify($this->proxy, 'other')->received(each(isOfType('int')));
    }
}
