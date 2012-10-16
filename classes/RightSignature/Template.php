<?php

namespace RightSignature;

use \RightSignature\Util\ArrayHelpers as ArrayHelpers;
use \RightSignature\Util\XmlHelpers as XmlHelpers;

class Template
{
	// ----------------------------------------
	// Prepackage Template

	/**
	 * Creates an intermediate document via the Prepackage Template call.
	 * @see https://rightsignature.com/apidocs/api_calls?api_method=prepackageTemplate
	 * @param object $client HTTP client
	 * @param string $templateGuid template GUID
	 * @param string $callbackUrl optional callback URL
	 * @return RightSignature\PrepackagedDocument
	 */
	public static function prepackage($client, $templateGuid, $callbackUrl=null)
	{
		$payload = $callbackUrl
			? "<callback_location>$callbackUrl</callback_location>"
			: null;
		$xml = $client->post("/api/templates/$templateGuid/prepackage.xml", $payload);

		$array = ArrayHelpers::normaliseKeys(XmlHelpers::toArray($xml));
		$array = ArrayHelpers::collapseGroup($array, 'merge_fields');
		$array = ArrayHelpers::collapseGroup($array, 'roles');
		$array = ArrayHelpers::collapseGroup($array, 'pages');

		return new PrepackagedDocument($client, $array);
	}
}