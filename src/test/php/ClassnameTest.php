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
 * Tests for the generated class names.
 *
 * @since  0.2.0
 */
class ClassnameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function doesNotGenerateClassTwice()
    {
        assertEquals(
                NewInstance::classname('\ReflectionObject'),
                NewInstance::classname('\ReflectionObject')
        );
    }
}
