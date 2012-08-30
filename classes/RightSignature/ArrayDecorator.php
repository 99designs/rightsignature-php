<?php

namespace RightSignature;

/**
 * A read-only wrapper around an array-tree that provides property-style
 * access. E.g.:
 *
 *   $a = array('foo_bar' => array('baz_bla' => 'quux'));
 *   
 *   // Instead of this:
 *   $quux = $a['foo_bar']['baz_bla'];
 *
 *   // Do something like this:
 *   $wrapped = new ArrayDecorator($a);
 *   $quux = $wrapped->foo_bar->baz_bla;
 */
class ArrayDecorator
	implements \ArrayAccess
{
	private $_array;

	public function __construct($array)
	{
		$this->_array = $array;
	}

	// ----------------------------------------
	// Property access

	public function __get($k)
	{
		return $this->_get($k);
	}

	public function __set($k, $v)
	{
		throw new \Exception('Unsupported operation');
	}

	// ----------------------------------------
	// ArrayAccess implementation

	public function offsetExists($k)
	{
		return isset($this->_array[$k]);
	}

	public function offsetGet($k)
	{
		return $this->_get($k);
	}

	public function offsetSet($k, $v)
	{
		throw new \Exception('Unsupported operation');
	}

	public function offsetUnset($k)
	{
		throw new \Exception('Unsupported operation');
	}

	// ----------------------------------------
	// Private

	private function _get($k)
	{
		$v = $this->_array[$k];
		return is_array($v) ? new self($v) : $v;
	}
}