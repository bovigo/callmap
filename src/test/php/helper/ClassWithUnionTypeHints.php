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
namespace bovigo\callmap\helper;
/**
 * Helper class for the test.
 *
 * @since 6.2.0
 */
class ClassWithUnionTypeHints
{
    /**
     * Example method to accept a union type hint.
     *
     * @param string|AnotherTestHelperClass $something
     */
    public function accept(string|AnotherTestHelperClass $something): void
    {
        // intentionally empty
    }

    /**
     * Example method to return a union type.
     *
     * @return int|float
     */
    public function doReturn(): int|float
    {
        return 1;
    }

    /**
     * Example method containing self.
     *
     * @param string|self $self
     */
    public function methodWithSelfParam(string|self $self): void
    {
        // intentionally empty
    }

    /**
     * Example method returning self.
     *
     * @return self|false
     */
    public function methodReturningSelf(): self|false
    {
        return $this;
    }
}

/**
 * Just an example function with union type hints.
 *
 * @param array|callable|null $extra
 * @return string|false|null
 */
function exampleFunctionWithUnionTypeHints(array|callable $extra = null): string|false|null
{
    return null;
}
