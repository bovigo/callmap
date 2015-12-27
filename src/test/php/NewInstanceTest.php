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
use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isNotSameAs;
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
if (PHP_MAJOR_VERSION >= 7) {
    class ReturnTypeHints
    {
        public function something(): array
        {
            return [];
        }
    }
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
        NewInstance::of(ThisIsNotPossible::class);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Can not create mapping proxy for final class bovigo\callmap\ThisIsNotPossible
     * @since  0.4.0
     */
    public function canNotCreateStubInstanceOfFinalClass()
    {
        NewInstance::stub(ThisIsNotPossible::class);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Can not create mapping proxy for final class bovigo\callmap\ThisIsNotPossible
     * @since  0.4.0
     */
    public function canNotRetrieveMappedClassnameForFinalClass()
    {
        NewInstance::classname(ThisIsNotPossible::class);
    }

    /**
     * @test
     * @since  0.2.0
     */
    public function doesNotGenerateClassTwice()
    {
        assert(
                NewInstance::classname(\ReflectionObject::class),
                equals(NewInstance::classname(\ReflectionObject::class))
        );
    }

    /**
     * @test
     * @since  0.2.0
     */
    public function doesCreateIndependentInstances()
    {
        assert(
                NewInstance::of(\ReflectionObject::class, [$this]),
                isNotSameAs(NewInstance::of(\ReflectionObject::class, [$this]))
        );
    }

    /**
     * @test
     * @since  0.2.0
     */
    public function doesCreateIndependentStubs()
    {
        assert(
                NewInstance::stub(AnotherTestHelperClass::class),
                isNotSameAs(NewInstance::stub(AnotherTestHelperClass::class))
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to map method bovigo\callmap\AnotherTestHelperClass::doesNotExist(), but it does not exist. Probably a typo?
     * @since  0.4.0
     */
    public function mapNonExistingMethodThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->mapCalls(['doesNotExist' => true]);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to map method bovigo\callmap\AnotherTestHelperClass::doSomethingy(), but it does not exist. Probably a typo?
     * @since  0.4.0
     */
    public function mapExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->mapCalls(['doSomethingy' => true]);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to map method bovigo\callmap\AnotherTestHelperClass::doNotTouchThis(), but it is not applicable for mapping.
     * @since  0.4.0
     */
    public function mapNonApplicableMethodThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->mapCalls(['doNotTouchThis' => true]);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve call amount for method bovigo\callmap\AnotherTestHelperClass::doesNotExist(), but it does not exist. Probably a typo?
     * @since  0.5.0
     */
    public function checkCallAmountForNonExistingMethodThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->callsReceivedFor('doesNotExist');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve call amount for method bovigo\callmap\AnotherTestHelperClass::doSomethingy(), but it does not exist. Probably a typo?
     * @since  0.5.0
     */
    public function checkCallAmountForExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->callsReceivedFor('doSomethingy');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve call amount for method bovigo\callmap\AnotherTestHelperClass::doNotTouchThis(), but it is not applicable for mapping.
     * @since  0.5.0
     */
    public function checkCallAmountForNonApplicableMethodThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->callsReceivedFor('doNotTouchThis');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve received arguments for method bovigo\callmap\AnotherTestHelperClass::doesNotExist(), but it does not exist. Probably a typo?
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForNonExistingMethodThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->argumentsReceivedFor('doesNotExist');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve received arguments for method bovigo\callmap\AnotherTestHelperClass::doSomethingy(), but it does not exist. Probably a typo?
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->argumentsReceivedFor('doSomethingy');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @expectedExceptionMessage  Trying to retrieve received arguments for method bovigo\callmap\AnotherTestHelperClass::doNotTouchThis(), but it is not applicable for mapping.
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForNonApplicableMethodThrowsInvalidArgumentException()
    {
        NewInstance::of(AnotherTestHelperClass::class)
                ->argumentsReceivedFor('doNotTouchThis');
    }

    /**
     * @test
     * @since  2.0.0
     * @requires  PHP 7.0.0
     */
    public function canCreateInstanceFromClassWithPhp7ReturnTypeHintOnMethod()
    {
        assert(
                NewInstance::of(ReturnTypeHints::class),
                isInstanceOf(ReturnTypeHints::class)
        );
    }
}
