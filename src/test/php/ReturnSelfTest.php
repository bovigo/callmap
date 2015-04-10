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
 * Tests for automated return self.
 *
 * @since  0.3.0
 */
class ReturnSelfTest extends \PHPUnit_Framework_TestCase
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
        $this->proxy = NewInstance::stub('bovigo\callmap\Extended');
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsThis()
    {
        assertSame($this->proxy, $this->proxy->yo());
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsFullyQualifiedClassName()
    {
        assertSame($this->proxy, $this->proxy->action());
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsFullyQualifiedClassNameWithoutLeadingBackslash()
    {
        assertSame($this->proxy, $this->proxy->moreAction());
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsNonFullyQualifiedClassName()
    {
        assertSame($this->proxy, $this->proxy->minorAction());
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsImplementedInterface()
    {
        assertSame($this->proxy, $this->proxy->foo());
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsInterfaceImplementedByParentClass()
    {
        assertSame($this->proxy, $this->proxy->wow());
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsParentClass()
    {
        assertSame($this->proxy, $this->proxy->aha());
    }

    /**
     * @test
     */
    public function returnsSelfIfMethodCallNotMappedWhenReturnTypeIsSelf()
    {
        assertSame($this->proxy, $this->proxy->baz());
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintIsTraversableEvenWhenInTypeHierarchy()
    {
        assertNull($this->proxy->getIterator());
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintIsNotInTypeHierarchy()
    {
        assertNull($this->proxy->other());
    }

    /**
     * @test
     */
    public function returnsSelfForInterfacesWhenCreatedWithInstanceOfAndAccordingReturnType()
    {
        $proxy = NewInstance::of('bovigo\callmap\OneMoreThing');
        assertSame($proxy, $proxy->wow());
    }

    /**
     * @test
     */
    public function doesNotReturnSelfWhenReturnTypeHintEmpty()
    {
        assertNull(NewInstance::of('bovigo\callmap\Bar')->foo());
    }
}
