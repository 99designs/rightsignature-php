<?php

namespace RightSignature;

class DocumentTest
	extends UnitTestCase
{
	// ----------------------------------------
	// Signer Links

	public function testSignerLinks()
	{
		$response = <<<EOS
			<document>
				<signer-links>
					<signer-link>
						<name>John Employee</name>
						<role>Signer</role>
						<signer-token>bg995X011CqtY44L</signer-token>
					</signer-link>
					<signer-link>
						<name>Susan Employee</name>
						<role>Signer</role>
						<signer-token>ad8XC0013d77d88X</signer-token>
					</signer-link>
				</signer-links>
			</document>
EOS;

		$client = \Mockery::mock('client');
		$client->shouldReceive('get')
			->with('/api/documents/1234/signer_links.xml')
			->andReturn($response);
		Document::signerLinks($client, '1234');

		$client = \Mockery::mock('client');
		$client->shouldReceive('get')
			->with('/api/documents/1234/signer_links.xml?redirect_location=http://example.com/')
			->andReturn($response);
		Document::signerLinks($client, '1234', 'http://example.com/');

		$client = \Mockery::mock('client');
		$client->shouldReceive('get')
			->andReturn($response);
		$signerLinks = Document::signerLinks($client, '1234');
		$this->assertEqual('John Employee', $signerLinks[0]->name);
		$this->assertEqual('ad8XC0013d77d88X', $signerLinks[1]->signer_token);
	}
}