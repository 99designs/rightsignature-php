<?php

namespace RightSignature;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->_apiToken = 'somefaketoken';
        parent::setUp();
    }

    public function testInstantiation()
    {
        $guzzleClient = new Client();

        $httpClient = HttpClient::forToken($this->_apiToken);

        $anotherHttpClient = HttpClient::forToken($this->_apiToken, $guzzleClient);

        $this->assertTrue($httpClient instanceof HttpClient);
        $this->assertTrue($anotherHttpClient instanceof HttpClient);
    }

    public function testGet()
    {
        $bodyText = 'some text';
        $mock = new MockHandler([
            new Response(200, [], $bodyText)
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $httpClient = HttpClient::forToken($this->_apiToken, $client);

        $response = $httpClient->get('fakeurl');

        $this->assertTrue($response === $bodyText);
    }

    public function testPost()
    {
        $postArray = [
            'some' => 'post'
        ];

        $postRaw = 'rawPost';

        $responseContent = 'someresponse';

        $mock = new MockHandler([
            new Response(200, [], $responseContent),
            new Response(200, [], $responseContent)
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $httpClient = HttpClient::forToken($this->_apiToken, $client);

        $response = $httpClient->post('fakeurl', $postRaw);
        $response2 = $httpClient->post('fakeulr', $postArray);

        $this->assertTrue($response === $responseContent);

        $this->assertTrue($response2 === $responseContent);
    }

    public function testInvalidRequestExceptionThrown()
    {
        $response = 'response';
        $responseContent = "<data><message>$response</message></data>";

        $mock = new MockHandler([
            new Response(406, [], $responseContent)
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $httpClient = HttpClient::forToken($this->_apiToken, $client);

        $this->setExpectedException('\RightSignature\Exception\InvalidRequest', $response);
        $httpClient->get('fakeUrl');
    }

    public function testRateLimitingExceptionThrown()
    {
        $response = 'invalid request';
        $responseContent = "<data><message>$response</message></data>";

        $mock = new MockHandler([
            new Response(400, [], $responseContent)
        ]);

        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        $httpClient = HttpClient::forToken($this->_apiToken, $client);

        $this->setExpectedException('\RightSignature\Exception\RateLimitExceeded', 'Rate limit exceeded');
        $httpClient->get('fakeUrl');
    }
}
