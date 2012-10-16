<?php

namespace RightSignature\Util;

class XmlHelpersTest
	extends \UnitTestCase
{
	public function testToArray()
	{
		$xml = <<<EOS
			<foo>
				<quux>42</quux>
				<bars>
					<bar>
						<baz>bla</baz>
					</bar>
					<bar>
						<baz>frobnicate</baz>
					</bar>
				</bars>
			</foo>
EOS;
			
		$array = XmlHelpers::toArray($xml);

		$this->assertEqual('42', $array['quux']);
		$this->assertEqual('bla', $array['bars']['bar'][0]['baz']);
		$this->assertEqual('frobnicate', $array['bars']['bar'][1]['baz']);
	}

	public function testToElement()
	{
		$expected = <<<EOS
			<foo>
				<quux bar="baz">
					<bla>A</bla>
					<bla>true</bla>
					<bla yadda="123">C</bla>
				</quux>
			</foo>
EOS;

		$array = array(
			'foo' => array(
				'quux' => array(
					'@attributes' => array('bar' => 'baz'),
					'bla' => array(
						'A',
						true,
						array(
							'@attributes' => array('yadda' => 123),
							'@value' => 'C'
						)
					),
				)
			),
		);

		$foo = XmlHelpers::toElement($array);

		$this->assertEqual('A', (string) $foo->quux->bla[0]);
		$this->assertEqual('baz', (string) $foo->quux['bar']);
		$this->assertEqual('123', (string) $foo->quux->bla[2]['yadda']);
	}
}