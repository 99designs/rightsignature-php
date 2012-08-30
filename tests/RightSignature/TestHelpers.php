<?php

namespace RightSignature;

/**
 * Extends UnitTestCase with additional helpers.
 */
class UnitTestCase
	extends \UnitTestCase
{
	/**
	 * Recursively tests equality of two DOMElements.
	 */
	public function assertEqualElements($a, $b)
	{
		
		foreach ($a->attributes() as $k)
			$this->assertEqual($a[$k], $b[$k]);
		
	}

	/**
	 * Performs a structural comparison of two XML strings.
	 */
	public static function equalXml($a, $b)
	{
		return self::equalElements(
			\DOMDocument::loadXML($a),
			\DOMDocument::loadXML($b)
		);
	}
}