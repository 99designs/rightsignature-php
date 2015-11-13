<?php

namespace RightSignature\Util;

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
class ArrayDecorator implements \ArrayAccess
{
	/**
	 * @var
     */
	private $_array;

	/**
	 * @param $array
     */
	public function __construct($array)
	{
		$this->_array = $array;
	}

	// ----------------------------------------
	// Property access

	/**
	 * @param $k
	 * @return ArrayDecorator
     */
	public function __get($k)
	{
		return $this->_get($k);
	}

	/**
	 * @param $k
	 * @param $v
	 * @throws \Exception
     */
	public function __set($k, $v)
	{
		throw new \Exception('Unsupported operation');
	}

	// ----------------------------------------
	// ArrayAccess implementation

	/**
	 * @param mixed $k
	 * @return bool
     */
	public function offsetExists($k)
	{
		return isset($this->_array[$k]);
	}

	/**
	 * @param mixed $k
	 * @return ArrayDecorator
     */
	public function offsetGet($k)
	{
		return $this->_get($k);
	}

	/**
	 * @param mixed $k
	 * @param mixed $v
	 * @throws \Exception
     */
	public function offsetSet($k, $v)
	{
		throw new \Exception('Unsupported operation');
	}

	/**
	 * @param mixed $k
	 * @throws \Exception
     */
	public function offsetUnset($k)
	{
		throw new \Exception('Unsupported operation');
	}

	/**
	 * @return mixed
     */
	public function asArray()
	{
		return $this->_array;
	}

	// ----------------------------------------
	// Private

	/**
	 * @param $k
	 * @return ArrayDecorator
     */
	private function _get($k)
	{
		$v = $this->_array[$k];
		return is_array($v) ? new self($v) : $v;
	}
}