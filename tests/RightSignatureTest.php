<?php

class RightSignatureTest
	extends UnitTestCase
{
	public function testEmbeddedWidgetUrl()
	{
		$this->assertEqual(
			'https://rightsignature.com/signatures/embedded?rt=1234',
			RightSignature::embeddedWidgetUrl('1234')
		);

		$this->assertEqual(
			'https://rightsignature.com/signatures/embedded?rt=1234&height=200',
			RightSignature::embeddedWidgetUrl('1234', 200)
		);
	}
}