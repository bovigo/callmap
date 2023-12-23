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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\each;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isOfType;
/**
 * Tests that mocks of traits can be created.
 */
#[Group('issue_1')]
class TraitTest extends TestCase
{
    /**
     * @var \bovigo\callmap\helper\TraitIncludingClass&ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::of(SomeTrait::class);
    }

    #[Test]
    public function callsOriginalMethodIfNoMappingProvided(): void
    {
        assertThat($this->proxy->action(313), equals(313));
    }

    #[Test]
    public function mapToSimpleValueReturnsValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => 'foo']);
        assertThat($this->proxy->action(313), equals('foo'));
    }

    #[Test]
    public function mapToClosureReturnsClosureReturnValueOnMethodCall(): void
    {
        $this->proxy->returns(['action' => function() { return 'foo'; }]);
        assertThat($this->proxy->action(313), equals('foo'));
    }

    #[Test]
    public function amountOfCallsToMethodIsZeroIfNotCalled(): void
    {
        verify($this->proxy, 'action')->wasNeverCalled();
    }

    #[Test]
    public function recordsAmountOfCallsToMethod(): void
    {
        $this->proxy->action(303);
        $this->proxy->action(313);
        verify($this->proxy, 'action')->wasCalled(2);
    }

    #[Test]
    public function returnsListOfReceivedArgumentsIfMethodCalled(): void
    {
        $this->proxy->action(313);
        verify($this->proxy, 'action')->received(313);
    }

    #[Test]
    public function optionalArgumentsCanNotBeVerifiedWhenNotExplicitlyPassed(): void
    {
        $this->proxy->other();
        verify($this->proxy, 'other')->receivedNothing();
    }

    #[Test]
    public function listOfReceivedArgumentsContainsGivenArguments(): void
    {
        $this->proxy->other(['play' => 808]);
        verify($this->proxy, 'other')->received(each(isOfType('int')));
    }
}
