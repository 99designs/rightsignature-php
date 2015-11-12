<?php

namespace RightSignature;


class CallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testParseDocumentSigned()
    {
        $xml = <<<EOS
			<callback>
				<callback-type>Document</callback-type>
				<guid>dl3jsdf9850dfkl3-dfl2</guid>
				<status>signed</status>
				<created-at>2009-11-05 16:36:08 -0800</created-at>
				<signed-at>2009-11-05 16:46:08 -0800</signed-at>
			</callback>
EOS;

        $callback = Callback::parse($xml);
        $this->assertTrue($callback->isDocument());
        $this->assertTrue($callback->isSigned());
        $this->assertEquals('dl3jsdf9850dfkl3-dfl2', $callback->guid);
    }

    public function testParseTemplateCreated()
    {
        $xml = <<<EOS
			<callback>
				<callback-type>Template</callback-type>
				<guid>dl3jsdf9850dfkl3-dfl2</guid>
				<status>created</status>
				<created-at>2009-11-05 16:36:08 -0800</created-at>
			</callback>
EOS;

        $callback = Callback::parse($xml);
        $this->assertTrue($callback->isTemplate());
        $this->assertTrue($callback->isCreated());
        $this->assertEquals('dl3jsdf9850dfkl3-dfl2', $callback->guid);
    }
}
