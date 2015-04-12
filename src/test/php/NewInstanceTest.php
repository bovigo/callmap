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
 * Helper class for the test.
 */
class AnotherTestHelperClass
{
    public function doSomething() { }

    private function doNotTouchThis() { }
}
/**
 * Another helper class.
 */
final class ThisIsNotPossible
{

}
/**
 * All remaining tests for bovigo\callmap\NewInstance.
 */
class NewInstanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function callWithNonObjectOrClassNameThrowsInvalidArgumentException()
    {
        NewInstance::of(313);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function callWithNonExistingClassNameThrowsInvalidArgumentException()
    {
        NewInstance::of('DoesNotExist');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Can not create mapping proxy for final class bovigo\callmap\ThisIsNotPossible
     * @since  0.4.0
     */
    public function canNotCreateInstanceOfFinalClass()
    {
        NewInstance::of('bovigo\callmap\ThisIsNotPossible');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Can not create mapping proxy for final class bovigo\callmap\ThisIsNotPossible
     * @since  0.4.0
     */
    public function canNotCreateStubInstanceOfFinalClass()
    {
        NewInstance::stub('bovigo\callmap\ThisIsNotPossible');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Can not create mapping proxy for final class bovigo\callmap\ThisIsNotPossible
     * @since  0.4.0
     */
    public function canNotRetrieveMappedClassnameForFinalClass()
    {
        NewInstance::classname('bovigo\callmap\ThisIsNotPossible');
    }

    /**
     * @test
     * @since  0.2.0
     */
    public function doesNotGenerateClassTwice()
    {
        assertEquals(
                NewInstance::classname('\ReflectionObject'),
                NewInstance::classname('\ReflectionObject')
        );
    }

    /**
     * @test
     * @since  0.2.0
     */
    public function doesCreateIndependentInstances()
    {
        assertNotSame(
                NewInstance::of('\ReflectionObject', [$this]),
                NewInstance::of('\ReflectionObject', [$this])
        );
    }

    /**
     * @test
     * @since  0.2.0
     */
    public function doesCreateIndependentStubs()
    {
        assertNotSame(
                NewInstance::stub('bovigo\callmap\AnotherTestHelperClass'),
                NewInstance::stub('bovigo\callmap\AnotherTestHelperClass')
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to map method "doesNotExist()", but it does not exist. Probably a typo?
     * @since  0.4.0
     */
    public function mapNonExistingMethodThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->mapCalls(['doesNotExist' => true]);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to map method "doSomethingy()", but it does not exist. Probably a typo?
     * @since  0.4.0
     */
    public function mapExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->mapCalls(['doSomethingy' => true]);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to map method "doNotTouchThis()", but it is not applicable for mapping.
     * @since  0.4.0
     */
    public function mapNonApplicableMethodThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->mapCalls(['doNotTouchThis' => true]);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve call amount for method "doesNotExist()", but it does not exist. Probably a typo?
     * @since  0.5.0
     */
    public function checkCallAmountForNonExistingMethodThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->callsReceivedFor('doesNotExist');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve call amount for method "doSomethingy()", but it does not exist. Probably a typo?
     * @since  0.5.0
     */
    public function checkCallAmountForExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->callsReceivedFor('doSomethingy');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve call amount for method "doNotTouchThis()", but it is not applicable for mapping.
     * @since  0.5.0
     */
    public function checkCallAmountForNonApplicableMethodThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->callsReceivedFor('doNotTouchThis');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve received arguments for method "doesNotExist()", but it does not exist. Probably a typo?
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForNonExistingMethodThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->argumentsReceivedFor('doesNotExist');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve received arguments for method "doSomethingy()", but it does not exist. Probably a typo?
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->argumentsReceivedFor('doSomethingy');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve received arguments for method "doNotTouchThis()", but it is not applicable for mapping.
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForNonApplicableMethodThrowsInvalidArgumentException()
    {
        NewInstance::of('bovigo\callmap\AnotherTestHelperClass')
                ->argumentsReceivedFor('doNotTouchThis');
    }
}
