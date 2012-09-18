<?php

namespace RightSignature;

/**
 * Extends UnitTestCase with additional helpers.
 */
abstract class UnitTestCase
	extends \UnitTestCase
{
	/**
	 * Structurally compares two XML strings.
	 */
	public function assertEqualXml($a, $b)
	{
		$this->assertEqual(
			XmlHelpers::toArray($a),
			XmlHelpers::toArray($b)
		);
	}
}