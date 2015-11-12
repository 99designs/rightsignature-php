<?php

namespace RightSignature;

use \RightSignature\Util\ArrayHelpers as ArrayHelpers;
use \RightSignature\Util\XmlHelpers as XmlHelpers;

/**
 * A RightSignature document instance.
 */
class Document extends \RightSignature\Util\ArrayDecorator
{
	const STATE_PENDING = 'pending';
	const STATE_SIGNED = 'signed';

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

	// ----------------------------------------
	// Document Details

	public static function documentDetails($client, $documentGuid)
	{
		$xml = $client->get(sprintf('/api/documents/%s.xml', $documentGuid));

		$array = ArrayHelpers::normaliseKeys(XmlHelpers::toArray($xml));
		$array = ArrayHelpers::collapseGroup($array, 'audit_trail');
		$array = ArrayHelpers::collapseGroup($array, 'form_fields');
		$array = ArrayHelpers::collapseGroup($array, 'recipients');
		$array = ArrayHelpers::collapseGroup($array, 'pages');

		return new self($array);
	}
}
