<?php

namespace RightSignature;

class TemplateTest
	extends UnitTestCase
{
	public function testPrepackage()
	{
		$response = <<<EOS
<?xml version="1.0" encoding="UTF-8"?>
<template>
  <guid>a_284964_14csd05aa7a3eb4f248772cd163b60cb17</guid>
  <redirect-token>24d8bb96a9144d319cc32f91e-4-14c05aa7a3eb4f243b60cb17</redirect-token>
  <subject>Sample NDA</subject>
  <message>Please sign this document.</message>
  <filename>disclosure.pdf</filename>
  <type>Document</type>
  <tags>nda</tags>
  <merge-fields>
    <merge-field>
      <page>1</page>
      <name>Your Name</name>
      <id>a_2843964_14c05aa7a3e72cd163b60cb17_10152369</id>
    </merge-field>
    <merge-field>
      <page>1</page>
      <name>Your Idea</name>
      <id>a_2843964_14c05aa7a3dfefd163b60cb17_10152370</id>
    </merge-field>
  </merge-fields>
  <roles>
    <role>
      <document-role-id>signer_A</document-role-id>
      <must-sign>true</must-sign>
      <is-sender>true</is-sender>
      <name>Document Sender</name>
    </role>
    <role>
      <document-role-id>signer_B</document-role-id>
      <must-sign>true</must-sign>
      <is-sender>false</is-sender>
      <name>Company Officer</name>
    </role>
  </roles>
  <content-type>pdf</content-type>
  <created-at>2010-10-10T14:41:08-07:00</created-at>
  <pages>
    <page>
      <page-number>1</page-number>
      <original-template-guid>a_2824964_14c05aa7ffrrecd163bx0cb17</original-template-guid>
      <original-template-filename>disclosure.pdf</original-template-filename>
    </page>
  </pages>
  <size>57740</size>
</template>
EOS;

		$self = $this;
		$client = \Mockery::mock('client');
		$client->shouldReceive('post')
			->with(
				'/api/templates/1234/prepackage.xml',
				\Mockery::on(function($body) use ($self) {
					$self->assertEqualXml('<callback_location>http://example.com/</callback_location>', $body);
					return true;
				})
			)
			->andReturn($response);

		$prepackaged = Template::prepackage($client, '1234', 'http://example.com/');

		$this->assertEqual('a_284964_14csd05aa7a3eb4f248772cd163b60cb17', $prepackaged->guid);
		$this->assertEqual('Your Name', $prepackaged->merge_fields[0]->name);
		$this->assertEqual('true', $prepackaged->roles[0]->is_sender);
		$this->assertEqual('disclosure.pdf', $prepackaged->pages[0]->original_template_filename);
	}
}