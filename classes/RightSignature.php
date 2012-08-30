<?php

use RightSignature\Document as Document;
use RightSignature\Template as Template;

/**
 * A partial implementation of the RightSignature API.
 * @see https://rightsignature.com/apidocs/overview
 * @author Stuart Campbell <stuart.campbell@99designs.com>
 */
class RightSignature
{
	const API_ENDPOINT = 'https://rightsignature.com';
	const API_VERSION = '1.2';

	private $_client;

	/**
	 * @param object $client client used to make API GET/POST requests
	 */
	public static function construct($client)
	{
		return new self($client);
	}

	/**
	 * @param object $client client used to make API GET/POST requests
	 */
	public function __construct($client)
	{
		$this->_client = $client;
	}

	// ----------------------------------------
	// Templates

	/**
	 * Returns a document template via the Template Details call.
	 * @see https://rightsignature.com/apidocs/api_calls?api_method=templateDetails
	 * @param string $templateGuid template GUID
	 * @return RightSignature\Template
	 */
	public function templateDetails($templateGuid)
	{
		//return Template::details($this->_client, $guid);
	}

	/**
	 * Returns a list of templates via the List Templates call.
	 * @see https://rightsignature.com/apidocs/api_calls?api_method=listTemplates
	 * @param string $templateGuid template GUID
	 * @return RightSignature\TemplateList
	 */
	public function listTemplates()
	{
		//return Template::list($this->_client);
	}

	/**
	 * Creates an intermediate document via the Prepackage Template call.
	 * @see https://rightsignature.com/apidocs/api_calls?api_method=prepackageTemplate
	 * @param string $templateGuid template GUID
	 * @param string $callbackUrl optional callback URL
	 * @return RightSignature\PrepackagedDocument
	 */
	public function prepackageTemplate($templateGuid, $callbackUrl=null)
	{
		return Template::prepackage($this->_client, $templateGuid, $callbackUrl);
	}

	// ----------------------------------------
	// Documents

	/**
	 * Returns document details via the Document Details call.
	 * @see https://rightsignature.com/apidocs/api_calls?api_method=documentDetails
	 * @param int $documentGuid document GUID
	 * @return RightSignature\Document
	 */
	public function document($documentGuid)
	{
		// return Document::details($this->_client, $documentGuid);
	}

	/**
	 * Return a list of all documents via the List Documents call.
	 * @see https://rightsignature.com/apidocs/api_calls?api_method=listDocuments
	 * @return RightSignature\DocumentList
	 */
	public function documents()
	{
		// return Document::list($this->_client);
	}
	
	// ----------------------------------------
	// Signer Links

	/**
	 * Generates signer links (used to generate embedded signing widgets)
	 * via the Signer Links call (undocumented on RightSignature website).
	 * @param string $documentGuid sent document GUID
	 * @param string $returnUrl option URL to redirect user to after signing
	 * @return RightSignature\SignerLinks
	 */
	public function signerLinks($documentGuid, $returnUrl=null)
	{
		return Document::signerLinks($this->_client, $documentGuid, $returnUrl);
	}

	// ----------------------------------------
	// Miscellany

	// XXX: Does this belong here?

	/**
	 * Generates a URL for an embedded signing <iframe>.
	 * @see RightSignature::signerLinks()
	 * @param string $signerToken signer token
	 * @param int $widgetHeight optional widget height (px)
	 * @return string
	 */
	public function embeddedWidgetUrl($signerToken, $widgetHeight=null)
	{
		$args = array("rt=$signerToken");
		if ($widgetHeight) $args []= "height=$widgetHeight";

		return sprintf('%s/signatures/embedded?%s',
			self::API_ENDPOINT,
			implode('&', $args)
		);
	}

	/**
	 * Parse an XML callback string.
	 * @param string $xml
	 * @return RightSignature\Callback
	 */
	public function parseCallback($xml)
	{
		return RightSignature\Callback::parse($xml);
	}
}
