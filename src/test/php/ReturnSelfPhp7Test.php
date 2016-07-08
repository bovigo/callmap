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
use function bovigo\assert\assert;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\assert\predicate\isNull;
interface Bar7
{
    /**
     * @return
     */
    public function foo();
}
interface OneMoreThing7
{
    public function wow(): OneMoreThing7;
}
class Base7 implements OneMoreThing7
{
    /**
     * @return self
     */
    public function baz()
    {
        // no actual return on purpose
    }

    public function aha(): Base7
    {
        // no actual return on purpose
    }

    public function wow(): OneMoreThing7
    {
        // no actual return on purpose
    }
}
class Extended7 extends Base7 implements Bar7, \IteratorAggregate
{
    public function foo(): Bar7
    {
        // no actual return on purpose
    }

    /**
     * @return $this
     */
    public function yo()
    {

    }

    public function action(): \bovigo\callmap\Extended7
    {
        // no actual return on purpose
    }

    public function moreAction(): \bovigo\callmap\Extended7
    {
        // no actual return on purpose
    }

    public function minorAction(): \bovigo\callmap\Extended7
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
     * @return  Other
     */
    public function other()
    {
        // no actual return on purpose
    }

}
/**
 * Tests for automated return self.
 *
 * @since  2.0.0
 */
class ReturnSelfPhp7Test extends \PHPUnit_Framework_TestCase
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
        $this->proxy = NewInstance::stub(Extended7::class);
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
        $proxy = NewInstance::of(OneMoreThing7::class);
        assert($proxy, isSameAs($proxy->wow()));
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintEmpty()
    {
        assert(NewInstance::of(Bar7::class)->foo(), isNull());
    }
}
