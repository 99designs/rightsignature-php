<?php

namespace RightSignature;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Communicates with the RightSignature HTTP endpoint.
 */
class HttpClient implements HttpClientInterface
{
	private $_client;
	private $_apiToken;

	public static function forToken($apiToken, ClientInterface $client = null)
	{
		if (! $client) {
			$client = new Client([
				'base_uri' => \RightSignature::API_ENDPOINT,
			]);
		}
		$instance = new self($client);
		$instance->setToken($apiToken);
		//$instance->_client->addHeader(sprintf('Api-Token: %s', $apiToken));
		return $instance;
	}

	private function __construct(ClientInterface $client)
	{
		$this->_client = $client;
	}

	/**
	 * @param string $path
	 * @return string
	 * @throws Exception
	 */
	public function get($path)
	{
		$response = $this->_client->get($path, [
			'headers' => [
				'Api-Token' => $this->_apiToken,
				'Api-Version' => \RightSignature::API_VERSION
			]
			$postRequest['verify'] = false;
		]);

		return $response;
	}

	public function setToken($apiToken)
	{
		$this->_apiToken = $apiToken;
	}

	/**
	 * @param string $path
	 * @param string $body
	 * @return string
	 * @throws Exception
	 */
	public function post($path, $body=null)
	{
		$postRequest['headers'] = [
			'Api-Token' => $this->_apiToken,
			'Api-Version' => \RightSignature::API_VERSION
		];
		$postRequest['verify'] = false;

		if (is_array($body)) {
			$postRequest['form_params'] = $body;
		}
		else {
			$postRequest['body'] = $body;
		}
		$response = $this->_client->post($path, $postRequest);

		return $response;
	}

	// ----------------------------------------
	// Private methods

	private function _submit($method /*, $args...*/)
	{
		$args = array_slice(func_get_args(), 1);
		try
		{
			$response = call_user_func_array(array($this->_client, $method), $args);
			return $response->getBody();
		}
		catch (\Exception $e)
		{
			throw self::_translateError($e);
		}
	}

	/**
	 * Translate an \Ergo\Http\Error into a domain-specific exception
	 * @param \Ergo\Http\Error $e
	 * @return \RightSignature\Exception
	 */
	private static function _translateError(\Exception $e)
	{
		switch ($e->getCode())
		{
			case 400:
				return new Exception\RateLimitExceeded();
			case 406:
				return Exception\InvalidRequest::fromXml($e->getMessage());
			default:
				return new Exception($e->getMessage());
		}
	}
}
