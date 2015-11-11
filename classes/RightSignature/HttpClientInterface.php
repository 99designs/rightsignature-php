<?php


namespace RightSignature;

use GuzzleHttp\ClientInterface;

interface HttpClientInterface
{
    public static function forToken($token, ClientInterface $client = null);

    public function get($path);

    public function post($path, $postData);
}