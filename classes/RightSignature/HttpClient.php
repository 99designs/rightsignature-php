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
    private $_verifySSL = true;

    /**
     * @param $apiToken
     * @param \GuzzleHttp\ClientInterface|null $client
     *
     * @return HttpClient
     */
    public static function forToken($apiToken, ClientInterface $client = null)
    {
        if (!$client) {
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
     * @param $bool
     */
    public function setVerifySSL($bool)
    {
        $this->_verifySSL = (bool) $bool;
    }

    /**
     * @param string $path
     *
     * @return string
     *
     * @throws Exception
     */
    public function get($path)
    {
        $getRequest = [
            $path, [
                'headers' => $this->_getHeaders(),
                'verify' => $this->_verifySSL,
            ],
        ];

        return $this->_submit('get', $getRequest);
    }

    /**
     * @param string $path
     * @param string $body
     *
     * @return string
     *
     * @throws Exception
     */
    public function post($path, $body = null)
    {
        // request options
        $options = [
            'headers' => $this->_getHeaders(),
            'verify' => $this->_verifySSL,
        ];

        // determinate data to send
        if (is_array($body)) {
            $options['form_params'] = $body;
        } else if (is_string($body)) {
            $options['body'] = $body;
        }

        $postRequest = [
            $path,
            $options,
        ];

        return $this->_submit('post', $postRequest);
    }

    /**
     * @return array headers for client call
     */
    private function _getHeaders()
    {
        return [
            'Api-Token' => $this->_apiToken,
            'Api-Version' => \RightSignature::API_VERSION,
            'Content-Type' => 'application/xml',
        ];
    }

    /**
     * @param $method
     * @param array $params
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function _submit($method, Array $params)
    {
        try {
            $response = call_user_func_array(array($this->_client, $method), $params);
            if ($response->getStatusCode() === 200) {
                return $response->getBody()->getContents();
            }

            // Or throw an exception
            throw self::_translateError($response->getStatusCode(), $response->getBody()->getContents());
        } catch (RequestException $ex) {
            if ($ex->hasResponse()) {
                $response = $ex->getResponse();
                throw self::_translateError($response->getStatusCode(), $response->getBody()->getContents());
            }

            // Default to a 400 error
            throw self::_translateError(400, 'Bad request');
        }
    }

    /**
     * Translate a rightsignature status code to a domain specific error
     * Error codes are based on https://rightsignature.com/apidocs/documentation_intro#/error_codes.
     *
     * @param $statusCode The status code of the call
     *
     * @return \RightSignature\Exception
     */
    private static function _translateError($statusCode, $message)
    {
        switch ($statusCode) {
            case 401:
                return new Exception\Unauthorized();
            case 403:
                return Exception\UserError::fromXml($message);
            case 406:
                return Exception\InvalidRequest::fromXml($message);
            case 429:
                return new Exception\RateLimitExceeded();
            case 500:
                return new Exception\ServerError();
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
