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
use bovigo\callmap\helper\Base;
use bovigo\callmap\helper\Fump;
use bovigo\callmap\helper\OneMoreThing;
use bovigo\callmap\helper\Really;
use bovigo\callmap\helper\WithSelfReturnTypeHint;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertNull;
use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\assert\predicate\isNull;
/**
 * Tests for automated return self.
 *
 * @since  0.3.0
 */
class ReturnSelfTest extends TestCase
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
        $this->proxy = NewInstance::stub(Extended::class);
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsThis()
    {
        assertThat($this->proxy, isSameAs($this->proxy->yo()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsFullyQualifiedClassName()
    {
        assertThat($this->proxy, isSameAs($this->proxy->action()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsFullyQualifiedClassNameWithoutLeadingBackslash()
    {
        assertThat($this->proxy, isSameAs($this->proxy->moreAction()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsNonFullyQualifiedClassName()
    {
        assertThat($this->proxy, isSameAs($this->proxy->minorAction()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsImplementedInterface()
    {
        assertThat($this->proxy, isSameAs($this->proxy->foo()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsInterfaceImplementedByParentClass()
    {
        assertThat($this->proxy, isSameAs($this->proxy->wow()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsParentClass()
    {
        assertThat($this->proxy, isSameAs($this->proxy->aha()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsSelf()
    {
        assertThat($this->proxy, isSameAs($this->proxy->baz()));
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintIsTraversableEvenWhenInTypeHierarchy()
    {
        assertThat($this->proxy->getIterator(), isNull());
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintIsNotInTypeHierarchy()
    {
        assertThat($this->proxy->other(), isNull());
    }

    /**
     * @test
     */
    public function returnsSelfForInterfacesWhenCreatedWithInstanceOfAndAccordingReturnType()
    {
        $proxy = NewInstance::of(OneMoreThing::class);
        assertThat($proxy, isSameAs($proxy->wow()));
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintEmpty()
    {
        assertNull(NewInstance::of(Bar::class)->foo());
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function doesNotReturnSelfWhenNoReturnTypeHintInDocComment()
    {
        assertNull(NewInstance::of(Fump::class)->noReturn());
    }

    /**
     * @test
     * @since  3.0.2
     * @group  self_type_hint
     */
    public function canWorkWithSelfReturnTypeHintForInterfaceDirectly()
    {
        assertThat(
                NewInstance::of(WithSelfReturnTypeHint::class)->wow(),
                isInstanceOf(WithSelfReturnTypeHint::class)
        );
    }

    /**
     * @test
     * @since  3.0.2
     * @group  self_type_hint
     */
    public function canWorkWithSelfReturnTypeHintForImplementingClass()
    {
        assertThat(
                NewInstance::stub(Really::class)->wow(),
                isInstanceOf(WithSelfReturnTypeHint::class)
        );
    }

    /**
     * @test
     * @since  3.0.2
     * @group  self_type_hint
     */
    public function canWorkWithSelfReturnTypeHintForClassDirectly()
    {
        assertThat(
                NewInstance::stub(Really::class)->hui(),
                isInstanceOf(Really::class)
        );
    }
}
