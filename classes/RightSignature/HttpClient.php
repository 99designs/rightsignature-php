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
	/**
	 * @var ClientInterface
     */
	private $_client;

	/**
	 * @var api token
     */
	private $_apiToken;

	/**
	 * @param The $apiToken
	 * @param \GuzzleHttp\ClientInterface|null $client
	 * @return HttpClient
     */
	public static function forToken($apiToken, ClientInterface $client = null)
	{
		if (! $client) {
			$client = new Client([
				'base_uri' => \RightSignature::API_ENDPOINT,
			]);
		}
		$instance = new self($client);
		$instance->setToken($apiToken);

		return $instance;
	}

	/**
	 * @param \GuzzleHttp\ClientInterface $client
     */
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
		$getRequest = [
			$path, [
				'headers' => [
					'Api-Token' => $this->_apiToken,
					'Api-Version' => \RightSignature::API_VERSION
				],
				'verify' => false
			]
		];

		return $this->_submit('get', $getRequest);
	}

	/**
	 * @param string $path
	 * @param string $body
	 * @return string
	 * @throws Exception
	 */
	public function post($path, $body=null)
	{
		$postRequest = [
			$path, [
				'headers' => [
					'Api-Token' => $this->_apiToken,
					'Api-Version' => \RightSignature::API_VERSION
				],
				'verify' => false
			]
		];

		if (is_array($body))
		{
			$postRequest['form_params'] = $body;
		}
		else
		{
			$postRequest['body'] = $body;
		}

		return $this->_submit('post', $postRequest);
	}

	/**
	 * @param $method
	 * @param array $params
	 * @return mixed
	 * @throws Exception
     */
	private function _submit($method, Array $params) {
		try
		{
			$response = call_user_func_array(array($this->_client, $method), $params);
			if ($response->getStatusCode() === 200) {
				return $response->getBody()->getContents();
			}

			// Or throw an exception
			throw self::_translateError($response->getStatusCode(), $response->getBody()->getContents());
		}
		catch (RequestException $ex)
		{
			if ($ex->hasResponse())
			{
				$response = $ex->getResponse();
				throw self::_translateError($response->getStatusCode(), $response->getBody()->getContents());
			}

			// Default to a 400 error
			throw self::_translateError(400);
		}
	}

	/**
	 * Translate a rightsignature status code to a domain specific error
	 * @param $statusCode The status code of the call
	 * @return \RightSignature\Exception
	 */
	private static function _translateError($statusCode, $message = 'Internal Server Error')
	{
		switch ($statusCode)
		{
			case 400:
				return new Exception\RateLimitExceeded();
			case 406:
				return Exception\InvalidRequest::fromXml($message);
			default:
				return new Exception($message);
		}
	}

	/**
	 * @param $apiToken
	 */
	private function setToken($apiToken)
	{
		$this->_apiToken = $apiToken;
	}
}
