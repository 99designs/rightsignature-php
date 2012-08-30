<?php

namespace RightSignature;

/**
 * 
 */
class Document
	extends ArrayDecorator
{
	private $_client;

	public function __construct($client, $data)
	{
		$this->_client = $client;
		parent::__construct($data);
	}

	// ----------------------------------------
	// Signer Links

	public static function signerLinks($client, $documentGuid, $returnUrl=null)
	{
		$url = sprintf('/api/documents/%s/signer_links.xml%s',
			$documentGuid,
			$returnUrl ? sprintf('?redirect_location=%s', $returnUrl) : ''
		);
		$xml = $client->get($url);

		$array = ArrayHelpers::normaliseKeys(XmlHelpers::toArray($xml));
		$array = ArrayHelpers::collapseGroup($array, 'signer_links');

		return new SignerLinks($client, $array['signer_links']);
	}
}