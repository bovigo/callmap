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

use Exception;

/**
 * Helper function for the test.
 */
function doSomething(): string
{
    return 'did something';
}
/**
 * Helper function for the test.
 */
function greet(string $whom)
{
    return 'Hello ' . $whom;
}
/**
 * Helper function for the test.
 */
function whichReturnsNothing(): void
{
    // intentionally empty
}
/**
 * Helper function for the test.
 */
function say(string $whom)
{
    return 'Hello ' . $whom;
}
/**
 * Helper function for the test.
 */
function withOptionalReturnValue(): ?Fump
{
    return null;
}
/**
 * Helper function for the test.
 */
function returnsVoid(): void
{
    // intentionally empty
}
/**
 * Helper function for the test.
 */
function returnsNever(): never
{
    throw new Exception('exception so this function never returns');
}
/**
 * Helper function for the test.
 */
function someOptionalString(): ?string
{
    return null;
}