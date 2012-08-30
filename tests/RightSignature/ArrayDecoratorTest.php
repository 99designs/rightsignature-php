<?php

namespace RightSignature;

class ArrayDecoratorTest
	extends UnitTestCase
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
		$this->assertEqual(42, $this->instance->foo->quux);

		//$this->expectException();
		//$this->instance->imaginary;
	}

	public function testSet()
	{
		$this->expectException();
		$this->instance->foo = 1;
	}

	// ----------------------------------------
	// Array access

	public function testOffsetGet()
	{
		$this->assertEqual('baz', $this->instance->foo->bar[0]);
	}

	public function testOffsetExists()
	{
		$this->assertTrue(isset($this->instance->foo->bar[0]));
		$this->assertFalse(isset($this->instance->foo->bar[3]));
	}

	public function testOffsetSet()
	{
		$this->expectException();
		$this->instance->foo->bar[1] = 'asdf';

		$this->expectException();
		$this->instance->foo->bar []= 'aaaa';
	}

	public function testOffsetUnset()
	{
		$this->expectException();
		unset($this->instance->foo->bar[1]);
	}
}