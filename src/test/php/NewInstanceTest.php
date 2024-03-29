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

use bovigo\callmap\helper\AnotherTestHelperClass;
use bovigo\callmap\helper\Extended7;
use bovigo\callmap\helper\ReturnTypeHints;
use bovigo\callmap\helper\ThisIsNotPossible;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionObject;

use function bovigo\assert\{
    assertNull,
    assertThat,
    expect,
    predicate\equals,
    predicate\isInstanceOf,
    predicate\isNotSameAs,
    predicate\isNull,
    predicate\isSameAs
};
/**
 * All remaining tests for bovigo\callmap\NewInstance.
 */
class NewInstanceTest extends TestCase
{
    #[Test]
    public function callWithNonExistingClassNameThrowsReflectionException(): void
    {
        expect(fn() => NewInstance::of('DoesNotExist'))
            ->throws(ReflectionException::class);

    }

    /**
     * @since 0.4.0
     */
    #[Test]
    public function canNotCreateInstanceOfFinalClass(): void
    {
        expect(fn() => NewInstance::of(ThisIsNotPossible::class))
            ->throws(InvalidArgumentException::class)
            ->withMessage('Can not create mapping proxy for final class ' . ThisIsNotPossible::class);
    }

    /**
     * @since 0.4.0
     */
    #[Test]
    public function canNotCreateStubInstanceOfFinalClass(): void
    {
        expect(fn() => NewInstance::stub(ThisIsNotPossible::class))
            ->throws(InvalidArgumentException::class)
            ->withMessage('Can not create mapping proxy for final class ' . ThisIsNotPossible::class);
    }

    /**
     * @since 0.4.0
     */
    #[Test]
    public function canNotRetrieveMappedClassnameForFinalClass(): void
    {
        expect(fn() => NewInstance::classname(ThisIsNotPossible::class))
            ->throws(InvalidArgumentException::class)
            ->withMessage('Can not create mapping proxy for final class ' . ThisIsNotPossible::class);
    }

    /**
     * @since 0.2.0
     */
    #[Test]
    public function doesNotGenerateClassTwice(): void
    {
        assertThat(
            NewInstance::classname(ReflectionObject::class),
            equals(NewInstance::classname(ReflectionObject::class))
        );
    }

    /**
     * @since 0.2.0
     */
    #[Test]
    public function doesCreateIndependentInstances(): void
    {
        assertThat(
            NewInstance::of(ReflectionObject::class, [$this]),
            isNotSameAs(NewInstance::of(ReflectionObject::class, [$this]))
        );
    }

    /**
     * @since 0.2.0
     */
    #[Test]
    public function doesCreateIndependentStubs(): void
    {
        assertThat(
            NewInstance::stub(AnotherTestHelperClass::class),
            isNotSameAs(NewInstance::stub(AnotherTestHelperClass::class))
        );
    }

    /**
     * @since 0.4.0
     */
    #[Test]
    public function mapNonExistingMethodThrowsInvalidArgumentException(): void
    {
        expect(fn() =>
            NewInstance::of(AnotherTestHelperClass::class)
                  ->returns(['doesNotExist' => true])
        )
            ->throws(\InvalidArgumentException::class)
            ->withMessage('Trying to map method ' . AnotherTestHelperClass::class.'::doesNotExist(), but it does not exist. Probably a typo?');
    }

    /**
     * @since 0.4.0
     */
    #[Test]
    public function mapExistingMethodWithTypoThrowsInvalidArgumentException(): void
    {
        expect(fn() =>
            NewInstance::of(AnotherTestHelperClass::class)
                ->returns(['doSomethingy' => true])
        )
            ->throws(InvalidArgumentException::class)
            ->withMessage('Trying to map method ' . AnotherTestHelperClass::class.'::doSomethingy(), but it does not exist. Probably a typo?');
    }

    /**
     * @since 0.4.0
     */
    #[Test]
    public function mapNonApplicableMethodThrowsInvalidArgumentException(): void
    {
        $proxy = NewInstance::of(AnotherTestHelperClass::class);
        expect(fn() => $proxy->returns(['doNotTouchThis' => true]))
            ->throws(InvalidArgumentException::class)
            ->withMessage(
                'Trying to map method '
                . AnotherTestHelperClass::class.'::doNotTouchThis(),'
                . ' but it is not applicable for mapping.'
            );
    }

    /**
     * @since 0.5.0
     */
    #[Test]
    public function retrieveInvocationsForNonExistingMethodThrowsInvalidArgumentException(): void
    {
        $proxy = NewInstance::of(AnotherTestHelperClass::class);
        expect(fn() => $proxy->invocations('doesNotExist'))
            ->throws(InvalidArgumentException::class)
            ->withMessage(
                'Trying to retrieve invocations for method '
                . AnotherTestHelperClass::class.'::doesNotExist(),'
                . ' but it does not exist. Probably a typo?'
            );
    }

    /**
     * @since 0.5.0
     */
    #[Test]
    public function retrieveInvocationsForExistingMethodWithTypoThrowsInvalidArgumentException(): void
    {
        $proxy = NewInstance::of(AnotherTestHelperClass::class);
        expect(fn() => $proxy->invocations('doSomethingy'))
            ->throws(InvalidArgumentException::class)
            ->withMessage(
                'Trying to retrieve invocations for method '
                . AnotherTestHelperClass::class.'::doSomethingy(),'
                . ' but it does not exist. Probably a typo?'
            );
    }

    /**
     * @since 0.5.0
     */
    #[Test]
    public function retrieveInvocationsForNonApplicableMethodThrowsInvalidArgumentException(): void
    {
        $proxy = NewInstance::of(AnotherTestHelperClass::class);
        expect(fn() => $proxy->invocations('doNotTouchThis'))
            ->throws(InvalidArgumentException::class)
            ->withMessage(
                'Trying to retrieve invocations for method '
                . AnotherTestHelperClass::class.'::doNotTouchThis(),'
                . ' but it is not applicable for mapping.'
            );
    }

    /**
     * @since 2.0.0
     */
    #[Test]
    public function canCreateInstanceFromClassWithPhp7ReturnTypeHintOnMethod(): void
    {
        assertThat(
            NewInstance::of(ReturnTypeHints::class),
            isInstanceOf(ReturnTypeHints::class)
        );
    }

    /**
     * @since 2.0.1
     */
    #[Test]
    public function mapReturnValueToNullShouldNotCallOriginalMethod(): void
    {
        $instance = NewInstance::of(AnotherTestHelperClass::class)
            ->returns(['gimmeFive' => null]);
        assertThat($instance->gimmeFive(), isNull());
    }

    /**
     * @since 5.0.2
     */
    #[Test]
    #[Group('optional_return_value')]
    public function canWorkWithOptionalReturnTypehints(): void
    {
        assertNull(NewInstance::of(ReturnTypeHints::class)->fump());
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('optional_return_value')]
    public function canWorkWithOptionalBuiltinReturnTypehints(): void
    {
        assertNull(NewInstance::of(ReturnTypeHints::class)->someString());
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('void_return')]
    public function canWorkWithVoidReturnTypehints(): void
    {
        expect(fn() => NewInstance::of(ReturnTypeHints::class)->returnsVoid())
            ->doesNotThrow();
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('void_return')]
    #[Group('stub')]
    public function voidMethodCanBeStubbed(): void
    {
        $i = NewInstance::of(Extended7::class)->stub('noAction');
        $i->noAction('example');
        verify($i, 'noAction')->received('example');
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('stub')]
    public function methodsWithOptionalReturnValueCanBeStubbed(): void
    {
        $i = NewInstance::of(Extended7::class)->stub('noAction', 'actionOptional');
        assertNull($i->actionOptional());
        verify($i, 'actionOptional')->receivedNothing();
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('stub')]
    public function methodsWithSelfReturnValueCanBeStubbed(): void
    {
        $i = NewInstance::of(Extended7::class)->stub('action');
        assertThat($i->action(), isSameAs($i));
        verify($i, 'action')->receivedNothing();
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('stub')]
    public function stubNonExistingMethodThrowsInvalidArgumentException(): void
    {
        expect(fn() => NewInstance::of(Extended7::class)->stub('doesNotExist'))
            ->throws(\InvalidArgumentException::class)
            ->withMessage(
                'Trying to stub method '
                . Extended7::class
                . '::doesNotExist(), but it does not exist. Probably a typo?'
            );
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('stub')]
    public function stubNonApplicableMethodThrowsInvalidArgumentException(): void
    {
        expect(fn() => NewInstance::of(AnotherTestHelperClass::class)->stub('doNotTouchThis'))
            ->throws(InvalidArgumentException::class)
            ->withMessage(
                'Trying to stub method '
                . AnotherTestHelperClass::class
                . '::doNotTouchThis(), but it is not applicable for stubbing.'
            );
    }

    /**
     * @since 5.1.0
     */
    #[Test]
    #[Group('stub')]
    public function stubMethodWhichWasAlreadyMappedThrowsInvalidArgumentException(): void
    {
        expect(fn() => 
            NewInstance::of(Extended7::class)
                ->returns(['action' => new Extended7()])
                ->stub('action')
        )
            ->throws(InvalidArgumentException::class)
            ->withMessage(
                'Trying to stub method '
                . Extended7::class
                . '::action(), but it was already mapped with a return value.'
            );
    }
}
