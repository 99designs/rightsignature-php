<?php

namespace RightSignature;

class Template
{
	private
		$_client,
		$_guid;

	/**
	 *
	 */
	public function __construct($client, $guid)
	{
		$this->_client = $client;
		$this->_guid = $guid;
	}

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