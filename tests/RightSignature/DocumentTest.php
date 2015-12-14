<?php

namespace RightSignature;

use PHPUnit_Framework_TestCase;

class DocumentTest extends PHPUnit_Framework_TestCase
{
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
            ->with(sprintf('/api/documents/1234/signer_links.xml?redirect_location=%s',
                urlencode('http://example.com/')
            ))
            ->andReturn($response);
        Document::signerLinks($client, '1234', 'http://example.com/');

        $client = \Mockery::mock('client');
        $client->shouldReceive('get')
            ->andReturn($response);
        $signerLinks = Document::signerLinks($client, '1234');
        $this->assertEquals('John Employee', $signerLinks[0]->name);
        $this->assertEquals('ad8XC0013d77d88X', $signerLinks[1]->signer_token);
    }

    public function testDocumentDetails()
    {
        $response = <<<EOS
        <document>
          <guid>LPARZRTTITSIBNNJHPSFTWX</guid>
          <subject>Employment Application</subject>
          <message>Please sign this document.</message>
          <state>pending</state>
          <tags>sendfromapi</tags>
          <original-filename>demo_document.pdf</original-filename>
          <recipients>
            <recipient>
              <role-id>cc_A</role-id>
              <email>support@rightsignature.com</email>
              <must-sign>false</must-sign>
              <is-sender>false</is-sender>
              <viewed-at></viewed-at>
              <name>RightSignature</name>
              <state>pending</state>
              <viewed-at></viewed-at>
              <completed-at></completed-at>
            </recipient>
          </recipients>
          <audit-trail>
            <audit-trail>
              <timestamp>10/09/2010 10:18PM PDT</timestamp>
              <message>Document emailed to John Bellingham (john.b@rightsignature.com)</message>
            </audit-trail>
          </audit-trail>
          <is-public>false</is-public>
          <expires-on>2010-10-15T17:00:00-07:00</expires-on>
          <deleted-at nil="true"></deleted-at>
          <original-url>https%3A%2F%2Fs3.amazonaws.com%3A443%2Fdocs.rightsignature.com/...</original-url>
          <content-type>api</content-type>
          <completed-at nil="true"></completed-at>
          <created-at>2010-10-09T22:18:54-07:00</created-at>
          <pages>
            <page>
              <original-template-guid>a_2842801_fc8471kide24d6bbdfgdf68d58fccb3</original-template-guid>
              <original-template-filename>demo_document.pdf</original-template-filename>
              <page-number>1</page-number>
            </page>
          </pages>
          <pdf-url>https%3A%2F%2Fs3.amazonaws.com%3A443%2Fdocs.rightsignature.com/...</pdf-url>
          <is-trashed>false</is-trashed>
          <callback-location>http://yoursite.com/doc_callback</callback-location>
          <thumbnail-url>https%3A%2F%2Fs3.amazonaws.com%3A443%2Fdocs.rightsignature.com/...</thumbnail-url>
          <size>5023</size>
          <processing-state>done-processing</processing-state>
          <signed-pdf-url></signed-pdf-url>
        </document>
EOS;

        $client = \Mockery::mock('client');
        $client->shouldReceive('get')
                ->with('/api/documents/1234.xml')
                ->andReturn($response);

        $document = Document::documentDetails($client, '1234');
        $this->assertEquals('support@rightsignature.com', $document->recipients[0]->email);
        $this->assertEquals('Document emailed to John Bellingham (john.b@rightsignature.com)', $document->audit_trail[0]->message);
    }

    public function testSendDocument()
    {
        $response = <<<EOS
            <document>
                <status>sent</status>
                <guid>2VMW88J3424MPEYF9DU6VY</guid>
            </document>
EOS;
        $payload = [
            'subject' => '- email subject -',
            'action' => 'send',
            'type' => 'base64',
            'recipients' => [
                'recipient' => [
                    [
                        'is_sender' => true,
                        'role' => 'cc',
                    ],
                    [
                        'name' => 'Signer 1',
                        'email' => 'pjafwcyv@sharklasers.com',
                        'role' => 'signer',
                    ],
                ],
            ],
        ];

        $client = \Mockery::mock('client');
        $client->shouldReceive('post')
                ->withAnyArgs()
                ->andReturn($response);

        $tmp = tmpfile();
        fwrite($tmp, '- test document to sign -');
        $meta = stream_get_meta_data($tmp);

        $document = Document::send($client, $meta['uri'], $payload);

        $this->assertEquals('sent', $document['status']);
    }
}
