<?php

namespace RightSignature\Exception;

class InvalidRequestTest
	extends \UnitTestCase
{
	public function testFromXml()
	{
		$xml = '<error><message>Sample message</message></error>';
		$ex = InvalidRequest::fromXml($xml);
		$this->assertEqual('Sample message', $ex->getMessage());
	}
}