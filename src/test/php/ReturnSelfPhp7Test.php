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

use bovigo\callmap\helper\Extended7;
use bovigo\callmap\helper\Bar7;
use bovigo\callmap\helper\OneMoreThing7;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertNull;
use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\assert\predicate\isNull;
/**
 * Tests for automated return self.
 *
 * @since 2.0.0
 */
class ReturnSelfPhp7Test extends TestCase
{
    /**
     * @var  Extended7<mixed>&ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(Extended7::class);
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsThis(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->yo()));
    }

    /**
     * @since 5.0.2
     */
    #[Test]
    #[Group('optional_return_value')]
    public function returnsNullIfMethodCallNotMappedWhenReturnTypeIsOptional(): void
    {
        assertNull($this->proxy->actionOptional());
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsFullyQualifiedClassName(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->action()));
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsFullyQualifiedClassNameWithoutLeadingBackslash(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->moreAction()));
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsNonFullyQualifiedClassName(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->minorAction()));
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsImplementedInterface(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->foo()));
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsInterfaceImplementedByParentClass(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->wow()));
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsParentClass(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->aha()));
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsSelf(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->baz()));
    }

    #[Test]
    public function doesNotReturnSelfWhenReturnTypeHintIsTraversableEvenWhenInTypeHierarchy(): void
    {
        assertThat($this->proxy->getIterator(), isNull());
    }

    #[Test]
    public function doesNotReturnSelfWhenReturnTypeHintIsNotInTypeHierarchy(): void
    {
        assertThat($this->proxy->other(), isNull());
    }

    #[Test]
    public function returnsSelfForInterfacesWhenCreatedWithInstanceOfAndAccordingReturnType(): void
    {
        $proxy = NewInstance::of(OneMoreThing7::class);
        assertThat($proxy, isSameAs($proxy->wow()));
    }

    #[Test]
    public function doesNotReturnSelfWhenReturnTypeHintEmpty(): void
    {
        assertThat(NewInstance::of(Bar7::class)->foo(), isNull());
    }
}
