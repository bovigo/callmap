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

use function bovigo\assert\expect;
use function bovigo\assert\predicate\contains;
/**
 * Test for bovigo\callmap\throws()
 *
 * @since  0.2.0
 */
class ThrowsTest extends TestCase
{
    /**
     * @var  \ReflectionObject&\bovigo\callmap\ClassProxy
     */
    private $proxy;

    protected function setUp(): void
    {
        $this->proxy = NewInstance::of(\ReflectionObject::class, [$this]);
    }

    /**
     * @test
     */
    public function throwsExceptionPassedViaThrows(): void
    {
        $e = new \ReflectionException('some error');
        $this->proxy->returns(['getName' => throws($e)]);
        expect(function() { $this->proxy->getName(); })
            ->throws(\ReflectionException::class)
            ->message(contains('some error'));

    }

    /**
     * @test
     */
    public function throwsExceptionPassedViaInvocationResults(): void
    {
        $e = new \ReflectionException('some error');
        $this->proxy->returns(
                ['getName' => onConsecutiveCalls('foo', throws($e))]
        );
        $this->proxy->getName(); // foo
        expect(function() { $this->proxy->getName(); }) // throws $e
            ->throws(\ReflectionException::class)
            ->message(contains('some error'));
    }
}
