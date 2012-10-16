<?php

namespace RightSignature;

class PrepackagedDocumentTest
	extends \RightSignature\UnitTestCase
{
	public function testPrefillAndSend()
	{
		$expectedRequest = <<<EOS
			<template>
				<guid>1234</guid>
				<subject>Example subject</subject>
				<action>send</action>
				<callback_location>http://example.com/</callback_location>
				<roles>
					<role role_name="Example role">
					<name>Joe Bloggs</name>
					<email>noemail@rightsignature.com</email>
					<locked>true</locked>
				</role>
					<role role_id="2345">
						<name>Jane Doe</name>
						<email>jane@example.com</email>
					</role>
				</roles>
				<merge_fields>
					<merge_field merge_field_name="Example field">
						<value>foo</value>
						<locked>true</locked>
					</merge_field>
					<merge_field merge_field_id="3456">
						<value>bar</value>
					</merge_field>
				</merge_fields>
			</template>
EOS;

		$response = <<<EOS
			<document>
				<status>sent</status>
				<guid>DJ53LF289DS23FF823J4</guid>
			</document>
EOS;

		$self = $this;
		$client = \Mockery::mock('client');
		$client->shouldReceive('post')
			->with(
				'/api/templates.xml',
				\Mockery::on(function ($body) use ($self, $expectedRequest) {
					$self->assertEqualXml($expectedRequest, $body);
					return true;
				})
			)
			->andReturn($response);

		$prepackaged = new PrepackagedDocument($client, array('guid' => '1234'));
		$guid = $prepackaged->prefillAndSend(array(
			'subject' => 'Example subject',
			'callback_location' => 'http://example.com/',
			'roles' => array(
				array(
					'role_name' => 'Example role',
					'name' => 'Joe Bloggs',
					'locked' => true,
				),
				array(
					'role_id' => '2345',
					'name' => 'Jane Doe',
					'email' => 'jane@example.com',
				),
			),
			'merge_fields' => array(
				array(
					'merge_field_name' => 'Example field',
					'value' => 'foo',
					'locked' => true,
				),
				array(
					'merge_field_id' => '3456',
					'value' => 'bar',
				),
			),
		));

		$this->assertEqual('DJ53LF289DS23FF823J4', $guid);
	}

	public function testPrefill()
	{
		$response = <<<EOS
			<template>
				<redirect-token>0b39aa811eca4ddeb89fd541c487ba79-2-8bffa095998e41ecbc420fb624b2fd</redirect-token>
				<guid>a_966_8bffa095998e41ecbdbc420fb624fd</guid>
				<pages>
					<page>
						<original-template-filename>disclosure.pdf</original-template-filename>
						<page-number>1</page-number>
						<original-template-guid>a_154_fqLTIoEbsaTWrJdEcNgcuIejFdhkVra</original-template-guid>
					</page>
					<page>
						<original-template-filename>Application.pdf</original-template-filename>
						<page-number>2</page-number>
						<original-template-guid>a_311_yDhmJDtNExtgJNiGVuSPQFadPoZjfeF</original-template-guid>
					</page>
				</pages>
				<type>DocumentPackage</type>
				<merge-fields>
					<merge-field>
						<page>1</page>
						<name>Company Name</name>
						<id>a_966_8bffa095998e41ecbdfb624b2fd_5671</id>
					</merge-field>
				</merge-fields>
				<created-at>2009-11-05T09:21:45-08:00</created-at>
				<roles>
					<role>
						<must-sign>false</must-sign>
						<document-role-id>cc_A</document-role-id>
						<role>Document Sender</role>
						<is-sender>true</is-sender>
					</role>
				</roles>
			</template>
EOS;

		$client = \Mockery::mock('client');
		$client->shouldReceive('post')
			->andReturn($response);

		$prepackaged = new PrepackagedDocument($client, array('guid' => '1234'));
		$document = $prepackaged->prefill(array(
			'subject' => 'anything',
			'roles' => array(
				array(
					'role_name' => 'foo',
					'name' => 'joe bloggs',
					'email' => 'bloggs@example.com',
				),
			),
		));

		$this->assertEqual('0b39aa811eca4ddeb89fd541c487ba79-2-8bffa095998e41ecbc420fb624b2fd', $document->redirect_token);
		$this->assertEqual('disclosure.pdf', $document->pages[0]->original_template_filename);
		$this->assertEqual('Company Name', $document->merge_fields[0]->name);
		$this->assertEqual('cc_A', $document->roles[0]->document_role_id);
	}
}