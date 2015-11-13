<?php

namespace RightSignature;

use GuzzleHttp\ClientInterface;

interface HttpClientInterface
{
    /**
     * @param $token The API token for RightSignature
     * @param ClientInterface|null $client
     *
     * @return \RightSignature\HttpClient
     */
    public static function forToken($token, ClientInterface $client = null);

    /**
     * @param $path The URL
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function get($path);

    /**
     * @param $path The URL of the route
     * @param $postData The post data (or an array of data)
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function post($path, $postData);
}
