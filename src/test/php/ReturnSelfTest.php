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

use bovigo\callmap\helper\Extended;
use bovigo\callmap\helper\Bar;
use bovigo\callmap\helper\Fump;
use bovigo\callmap\helper\OneMoreThing;
use bovigo\callmap\helper\Really;
use bovigo\callmap\helper\WithSelfReturnTypeHint;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertNull;
use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\assert\predicate\isNull;
/**
 * Tests for automated return self.
 *
 * @since 0.3.0
 */
class ReturnSelfTest extends TestCase
{
    /**
     * @var  Extended<mixed>&ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::stub(Extended::class);
    }

    #[Test]
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsThis(): void
    {
        assertThat($this->proxy, isSameAs($this->proxy->yo()));
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
        $proxy = NewInstance::of(OneMoreThing::class);
        assertThat($proxy, isSameAs($proxy->wow()));
    }

    #[Test]
    public function doesNotReturnSelfWhenReturnTypeHintEmpty(): void
    {
        assertNull(NewInstance::of(Bar::class)->foo());
    }

    /**
     * @since 3.0.0
     */
    #[Test]
    public function doesNotReturnSelfWhenNoReturnTypeHintInDocComment(): void
    {
        assertNull(NewInstance::of(Fump::class)->noReturn());
    }

    /**
     * @since 3.0.2
     */
    #[Test]
    #[Group('self_type_hint')]
    public function canWorkWithSelfReturnTypeHintForInterfaceDirectly(): void
    {
        assertThat(
            NewInstance::of(WithSelfReturnTypeHint::class)->wow(),
            isInstanceOf(WithSelfReturnTypeHint::class)
        );
    }

    /**
     * @since 3.0.2
     */
    #[Test]
    #[Group('self_type_hint')]
    public function canWorkWithSelfReturnTypeHintForImplementingClass(): void
    {
        assertThat(
            NewInstance::stub(Really::class)->wow(),
            isInstanceOf(WithSelfReturnTypeHint::class)
        );
    }

    /**
     * @since 3.0.2
     */
    #[Test]
    #[Group('self_type_hint')]
    public function canWorkWithSelfReturnTypeHintForClassDirectly(): void
    {
        assertThat(
            NewInstance::stub(Really::class)->hui(),
            isInstanceOf(Really::class)
        );
    }
}
