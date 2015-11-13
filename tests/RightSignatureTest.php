<?php

class RightSignatureTest extends \Tests\UnitTestcase
{
    public function testEmbeddedWidgetUrl()
    {
        $this->assertEquals(
            'https://rightsignature.com/signatures/embedded?rt=1234',
            RightSignature::embeddedWidgetUrl('1234')
        );

        $this->assertEquals(
            'https://rightsignature.com/signatures/embedded?rt=1234&height=200',
            RightSignature::embeddedWidgetUrl('1234', 200)
        );
    }
}
