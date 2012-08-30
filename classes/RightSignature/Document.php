<?php

namespace RightSignature;

/**
 * A RightSignature document instance.
 */
class Document
	extends ArrayDecorator
{
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

		return new SignerLinks($array['signer_links']);
	}
}