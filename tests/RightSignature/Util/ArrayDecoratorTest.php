<?php

namespace RightSignature\Util;

class ArrayDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->instance = new ArrayDecorator(array(
            'foo' => array(
                'bar' => array(
                    'baz',
                    'bla',
                ),
                'quux' => 42,
            ),
        ));
    }

    // ----------------------------------------
    // Property access

    public function testGet()
    {
        $this->assertEquals(42, $this->instance->foo->quux);

        //$this->expectException();
        //$this->instance->imaginary;
    }

    public function testSet()
    {
        $this->setExpectedException('Exception');
        $this->instance->foo = 1;
    }

    // ----------------------------------------
    // Array access

    public function testOffsetGet()
    {
        $this->assertEquals('baz', $this->instance->foo->bar[0]);
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->instance->foo->bar[0]));
        $this->assertFalse(isset($this->instance->foo->bar[3]));
    }

    public function testOffsetSet()
    {
        $this->setExpectedException('Exception');
        $this->instance->foo->bar[1] = 'asdf';

        $this->setExpectedException('Exception');
        $this->instance->foo->bar [] = 'aaaa';
    }

    public function testOffsetUnset()
    {
        $this->setExpectedException('Exception');
        unset($this->instance->foo->bar[1]);
    }

    public function testAsArray()
    {
        $this->assertTrue(is_array($this->instance->asArray()));
    }
}
