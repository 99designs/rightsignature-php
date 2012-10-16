<?php

namespace RightSignature;

use \RightSignature\Util\ArrayHelpers as ArrayHelpers;
use \RightSignature\Util\XmlHelpers as XmlHelpers;

/**
 * A RightSignature document instance.
 */
class Document
	extends \RightSignature\Util\ArrayDecorator
{
	// ----------------------------------------
	// Signer Links

	public static function signerLinks($client, $documentGuid, $returnUrl=null)
	{
		$url = sprintf('/api/documents/%s/signer_links.xml%s',
			$documentGuid,
			$returnUrl ? sprintf('?redirect_location=%s', urlencode($returnUrl)) : ''
		);
		$xml = $client->get($url);

		$array = ArrayHelpers::normaliseKeys(XmlHelpers::toArray($xml));
		$array = ArrayHelpers::collapseGroup($array, 'signer_links');

		return new SignerLinks($array['signer_links']);
	}
}