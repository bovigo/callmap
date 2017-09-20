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

use function bovigo\assert\assert;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\assert\predicate\isNull;
/**
 * Helper interface for the test below.
 */
interface Bar
{
    /**
     * @return
     */
    public function foo();
}
/**
 * Helper interface for the test below.
 */
interface OneMoreThing
{
    /**
     * @return  OneMoreThing
     */
    public function wow();
}
/**
 * Helper class for the test below.
 */
class Base implements OneMoreThing
{
    /**
     * @return self
     */
    public function baz()
    {
        // no actual return on purpose
    }

    /**
     * @return  Base
     */
    public function aha()
    {
        // no actual return on purpose
    }

    /**
     * @return  OneMoreThing
     */
    public function wow()
    {
        // no actual return on purpose
    }
}
/**
 * Helper class for the test below.
 */
class Extended extends Base implements Bar, \IteratorAggregate
{
    /**
     * @return  Bar
     */
    public function foo()
    {
        // no actual return on purpose
    }

    /**
     * @return $this
     */
    public function yo()
    {

    }

    /**
     * @return  \bovigo\callmap\Extended
     */
    public function action()
    {
        // no actual return on purpose
    }

    /**
     * @return  bovigo\callmap\Extended
     */
    public function moreAction()
    {
        // no actual return on purpose
    }

    /**
     * @return  Extended
     */
    public function minorAction()
    {
        // no actual return on purpose
    }

    /**
     * @return  \Traversable
     */
    public function getIterator()
    {
        // no actual return on purpose
    }

    /**
     * @return Other
     */
    public function other()
    {
        // no actual return on purpose
    }

}
/**
 * @since  3.0.0
 */
interface Fump
{
    /**
     *
     */
    public function noReturn();
}
/**
 * @since  3.0.2
 */
interface WithSelfReturnTypeHint
{
    public function wow(): self;
}
/**
 * @since  3.0.2
 */
class Really implements WithSelfReturnTypeHint
{
    public function wow(): WithSelfReturnTypeHint { return $this; }

    public function hui(): self { return $this; }
}
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
    public function setUp()
    {
        $this->proxy = NewInstance::stub(Extended::class);
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsThis()
    {
        assert($this->proxy, isSameAs($this->proxy->yo()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsFullyQualifiedClassName()
    {
        assert($this->proxy, isSameAs($this->proxy->action()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsFullyQualifiedClassNameWithoutLeadingBackslash()
    {
        assert($this->proxy, isSameAs($this->proxy->moreAction()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsNonFullyQualifiedClassName()
    {
        assert($this->proxy, isSameAs($this->proxy->minorAction()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsImplementedInterface()
    {
        assert($this->proxy, isSameAs($this->proxy->foo()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsInterfaceImplementedByParentClass()
    {
        assert($this->proxy, isSameAs($this->proxy->wow()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsParentClass()
    {
        assert($this->proxy, isSameAs($this->proxy->aha()));
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsSelf()
    {
        assert($this->proxy, isSameAs($this->proxy->baz()));
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintIsTraversableEvenWhenInTypeHierarchy()
    {
        assert($this->proxy->getIterator(), isNull());
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintIsNotInTypeHierarchy()
    {
        assert($this->proxy->other(), isNull());
    }

    /**
     * @test
     */
    public function returnsSelfForInterfacesWhenCreatedWithInstanceOfAndAccordingReturnType()
    {
        $proxy = NewInstance::of(OneMoreThing::class);
        assert($proxy, isSameAs($proxy->wow()));
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintEmpty()
    {
        assert(NewInstance::of(Bar::class)->foo(), isNull());
    }

    /**
     * @test
     * @since  3.0.0
     */
    public function doesNotReturnSelfWhenNoReturnTypeHintInDocComment()
    {
        assert(NewInstance::of(Fump::class)->noReturn(), isNull());
    }

    /**
     * @test
     * @since  3.0.2
     * @group  self_type_hint
     */
    public function canWorkWithSelfReturnTypeHintForInterfaceDirectly()
    {
        assert(
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
        assert(
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
        assert(
                NewInstance::stub(Really::class)->hui(),
                isInstanceOf(Really::class)
        );
    }
}
