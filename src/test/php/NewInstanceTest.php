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
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isNotSameAs;
use function bovigo\assert\predicate\isNull;
/**
 * Helper class for the test.
 */
class AnotherTestHelperClass
{
    public function doSomething() { }

    private function doNotTouchThis() { }

    public function gimmeFive()
    {
        return 5;
    }
}
/**
 * Another helper class.
 */
final class ThisIsNotPossible
{

}
/**
 * One more helper, this time with a PHP 7 return type hint.
 *
 * @since  2.0.0
 */
class ReturnTypeHints
{
    public function something(): array
    {
        return [];
    }
}
/**
 * All remaining tests for bovigo\callmap\NewInstance.
 */
class NewInstanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function callWithNonObjectOrClassNameThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(313);
        })
        ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function callWithNonExistingClassNameThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of('DoesNotExist');
        })
        ->throws(\InvalidArgumentException::class);

    }

    /**
     * @test
     * @since  0.4.0
     */
    public function canNotCreateInstanceOfFinalClass()
    {
        expect(function() {
                NewInstance::of(ThisIsNotPossible::class);
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Can not create mapping proxy for final class ' . ThisIsNotPossible::class);
    }

    /**
     * @test
     * @since  0.4.0
     */
    public function canNotCreateStubInstanceOfFinalClass()
    {
        expect(function() {
                NewInstance::stub(ThisIsNotPossible::class);
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Can not create mapping proxy for final class ' . ThisIsNotPossible::class);
    }

    /**
     * @test
     * @since  0.4.0
     */
    public function canNotRetrieveMappedClassnameForFinalClass()
    {
        expect(function() {
                NewInstance::classname(ThisIsNotPossible::class);
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Can not create mapping proxy for final class ' . ThisIsNotPossible::class);
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
     * @since  0.4.0
     */
    public function mapNonExistingMethodThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->mapCalls(['doesNotExist' => true]);
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to map method bovigo\callmap\AnotherTestHelperClass::doesNotExist(), but it does not exist. Probably a typo?');
    }

    /**
     * @test
     * @since  0.4.0
     */
    public function mapExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->mapCalls(['doSomethingy' => true]);
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to map method bovigo\callmap\AnotherTestHelperClass::doSomethingy(), but it does not exist. Probably a typo?');
    }

    /**
     * @test
     * @since  0.4.0
     */
    public function mapNonApplicableMethodThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->mapCalls(['doNotTouchThis' => true]);
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to map method bovigo\callmap\AnotherTestHelperClass::doNotTouchThis(), but it is not applicable for mapping.');
    }

    /**
     * @test
     * @since  0.5.0
     */
    public function checkCallAmountForNonExistingMethodThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->callsReceivedFor('doesNotExist');
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to retrieve call amount for method bovigo\callmap\AnotherTestHelperClass::doesNotExist(), but it does not exist. Probably a typo?');
    }

    /**
     * @test
     * @since  0.5.0
     */
    public function checkCallAmountForExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->callsReceivedFor('doSomethingy');
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to retrieve call amount for method bovigo\callmap\AnotherTestHelperClass::doSomethingy(), but it does not exist. Probably a typo?');
    }

    /**
     * @test
     * @since  0.5.0
     */
    public function checkCallAmountForNonApplicableMethodThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->callsReceivedFor('doNotTouchThis');
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to retrieve call amount for method bovigo\callmap\AnotherTestHelperClass::doNotTouchThis(), but it is not applicable for mapping.');
    }

    /**
     * @test
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForNonExistingMethodThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->argumentsReceivedFor('doesNotExist');
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to retrieve received arguments for method bovigo\callmap\AnotherTestHelperClass::doesNotExist(), but it does not exist. Probably a typo?');
    }

    /**
     * @test
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForExistingMethodWithTypoThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->argumentsReceivedFor('doSomethingy');
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to retrieve received arguments for method bovigo\callmap\AnotherTestHelperClass::doSomethingy(), but it does not exist. Probably a typo?');
    }

    /**
     * @test
     * @since  0.5.0
     */
    public function retrieveReceivedArgumentsForNonApplicableMethodThrowsInvalidArgumentException()
    {
        expect(function() {
                NewInstance::of(AnotherTestHelperClass::class)
                        ->argumentsReceivedFor('doNotTouchThis');
        })
        ->throws(\InvalidArgumentException::class)
        ->withMessage('Trying to retrieve received arguments for method bovigo\callmap\AnotherTestHelperClass::doNotTouchThis(), but it is not applicable for mapping.');
    }

    /**
     * @test
     * @since  2.0.0
     */
    public function canCreateInstanceFromClassWithPhp7ReturnTypeHintOnMethod()
    {
        assert(
                NewInstance::of(ReturnTypeHints::class),
                isInstanceOf(ReturnTypeHints::class)
        );
    }

    /**
     * @test
     * @since  2.0.1
     */
    public function mapReturnValueToNullShouldNotCallOriginalMethod()
    {
        $instance = NewInstance::of(AnotherTestHelperClass::class)
                ->mapCalls(['gimmeFive' => null]);
        assert($instance->gimmeFive(), isNull());
    }
}
