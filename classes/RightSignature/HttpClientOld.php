<?php
namespace RightSignature;
/**
 * Communicates with the RightSignature HTTP endpoint.
 */
class HttpClientOld
{
    private $_client;
    public static function forToken($apiToken)
    {
        $instance = new self();
        $instance->_client->addHeader(sprintf('Api-Token: %s', $apiToken));
        return $instance;
    }
    public function __construct()
    {
        $this->_client = new \Ergo\Http\Client(\RightSignature::API_ENDPOINT);
        $this->_client->addHeader(sprintf('Api-Version: %s', \RightSignature::API_VERSION));
    }
    /**
     * @param string $path
     * @return string
     * @throws RightSignature\Exception
     */
    public function get($path)
    {
        $response = $this->_submit('get', $path);
        return $response;
    }
    /**
     * @param string $path
     * @param string $body
     * @return string
     * @throws RightSignature\Exception
     */
    public function post($path, $body=null)
    {
        $response = $this->_submit('post', $path, $body, $body ? 'application/xml' : null);
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
        catch (\Ergo\Http\Error $e)
        {
            throw self::_translateError($e);
        }
    }
    /**
     * Translate an \Ergo\Http\Error into a domain-specific exception
     * @param \Ergo\Http\Error $e
     * @return \RightSignature\Exception
     */
    private static function _translateError($e)
    {
        switch ($e->getStatusCode())
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