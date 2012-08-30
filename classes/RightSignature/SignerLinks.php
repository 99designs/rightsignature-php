<?php

namespace RightSignature;

class SignerLinks
	extends ArrayDecorator
{
	private $_client;

	public function __construct($client, $data)
	{
		$this->_client = $client;
		parent::__construct($data);
	}
}