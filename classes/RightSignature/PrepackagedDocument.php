<?php

namespace RightSignature;

/**
 * An intermediate document returned from a Prepackage Template call.
 * @see https://rightsignature.com/apidocs/api_calls?api_method=prepackageTemplate
 */
class PrepackagedDocument
	extends ArrayDecorator
{
	const ACTION_SEND = 'send';
	const ACTION_PREFILL = 'prefill';

	const EMBEDDED_SIGNING_EMAIL = 'noemail@rightsignature.com';

	private $_client;

	public function __construct($client, $data)
	{
		$this->_client = $client;
		parent::__construct($data);
	}

	/**
	 * Creates a prefilled document via the Prefill Template call.
	 * 
	 * $args is an array like:
	 *
	 *   array(
	 *     'subject' => '...',
	 *     'roles' => array(
	 *       array(
	 *         'role_name' => '...',         // or role_id
	 *         'name' => '...',
	 *         'email' => '...',             // omit for embedded signing
	 *       ),
	 *       // ...
	 *     ),
	 *     'merge_fields' => array(
	 *       array(
	 *         'merge_field_name' => '...', // or merge_field_id
	 *         'value' => '...',
	 *         'locked => true,             // optional
	 *       ),
	 *     ),
	 *     // other args per API documentatation
	 *   )
	 * 
	 * @see https://rightsignature.com/apidocs/api_calls?api_method=prefillTemplate
	 * @param array $args
	 * @return RightSignature\Document
	 */
	public function prefill($args)
	{
		$payload = array_merge($args, array(
			'guid' => $this->guid,
			'action' => self::ACTION_PREFILL,
		));
		$body = self::_preparePrefillRequest($payload);
		$response = $this->_client->post('/api/templates.xml', $body);
		return new Document(self::_parsePrefillResponse($response));
	}

	/**
	 * Creates a prefilled document via the Prefill Template call and sends it for
	 * signing.
	 * 
	 * See prefill() for expected argument structure.
	 * 
	 * @see https://rightsignature.com/apidocs/api_calls?api_method=prefillTemplate
	 * @param array $args
	 * @return string sent document GUID
	 */
	public function prefillAndSend($args)
	{
		$payload = array_merge($args, array(
			'guid' => $this->guid,
			'action' => self::ACTION_SEND,
		));

		foreach ($payload['roles'] as &$role)
			if (!isset($role['email']))
				$role['email'] = self::EMBEDDED_SIGNING_EMAIL;

		$body = self::_preparePrefillRequest($payload);

		$xml = $this->_client->post('/api/templates.xml', $body);
		$data = XmlHelpers::toArray($xml);
		assert($data['status'] == 'sent');
		return $data['guid'];
	}

	// ----------------------------------------
	// Private

	/**
	 * Transform an array-tree of args into a Prefill Template XML payload.
	 * @param array $args
	 * @return string
	 */
	private static function _preparePrefillRequest($args)
	{
		foreach (array('guid', 'action', 'subject', 'roles') as $k)
			ArrayHelpers::ensureIsSet($args, $k);

		$roles = $args['roles'];
		foreach ($roles as &$role)
		{
			$idKey = isset($role['role_name']) ? 'role_name' : 'role_id';
			$role['@attributes'] = array($idKey => $role[$idKey]);
			unset($role[$idKey]);
		}
		$args['roles'] = array('role' => $roles);

		if (isset($args['merge_fields']))
		{
			$mergeFields = $args['merge_fields'];
			foreach ($mergeFields as &$mergeField)
			{
				$idKey = isset($mergeField['merge_field_name']) ? 'merge_field_name' : 'merge_field_id';
				$mergeField['@attributes'] = array($idKey => $mergeField[$idKey]);
				unset($mergeField[$idKey]);
			}
			$args['merge_fields'] = array('merge_field' => $mergeFields);
		}

		// TODO: tags

		return XmlHelpers::toXml(array('template' => $args));
	}

	/**
	 * Parse the XML response of a Prefill Template call.
	 */
	private static function _parsePrefillResponse($xml)
	{
		$array = ArrayHelpers::normaliseKeys(XmlHelpers::toArray($xml));
		$array = ArrayHelpers::collapseGroup($array, 'merge_fields');
		$array = ArrayHelpers::collapseGroup($array, 'roles');
		$array = ArrayHelpers::collapseGroup($array, 'pages');
		return $array;
	}
}