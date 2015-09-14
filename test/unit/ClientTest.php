<?php
/*
 * This file is part of Guzzle HTTP JSON-RPC
 *
 * Copyright (c) 2014 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/guzzle-jsonrpc/blob/master/LICENSE
 * @link http://github.com/graze/guzzle-jsonrpc
 */
namespace Graze\GuzzleHttp\JsonRpc;

use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Test\UnitTestCase;
use Mockery;

class ClientTest extends UnitTestCase
{
    public function setup()
    {
        $this->httpClient = $this->mockHttpClient();
        $this->httpHandler = $this->mockHttpHandler();
        $this->messageFactory = $this->mockMessageFactory();

        $this->httpClient->shouldReceive('getConfig')->once()->with('handler')->andReturn($this->httpHandler);
        $this->httpHandler->shouldReceive('push')->times(4);

        $this->client = new Client($this->httpClient, $this->messageFactory);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\ClientInterface', $this->client);
    }

    public function testStaticFactory()
    {
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\ClientInterface', Client::factory('http://foo'));
    }

    public function testNotification()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo'];
        $type = RequestInterface::NOTIFICATION;
        $uri = 'http://foo';

        $this->httpClient->shouldReceive('getConfig')->once()->with('base_uri')->andReturn($uri);
        $this->httpClient->shouldReceive('getConfig')->once()->with('defaults')->andReturn([]);
        $this->messageFactory->shouldReceive('createRequest')->once()->with($type, $uri, [], $jsonrpc)->andReturn($request);

        $this->assertSame($request, $this->client->notification('foo'));
    }

    public function testNotificationWithParams()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo', 'params'=>['bar'=>true]];
        $type = RequestInterface::NOTIFICATION;
        $uri = 'http://foo';

        $this->httpClient->shouldReceive('getConfig')->once()->with('base_uri')->andReturn($uri);
        $this->httpClient->shouldReceive('getConfig')->once()->with('defaults')->andReturn([]);
        $this->messageFactory->shouldReceive('createRequest')->once()->with($type, $uri, [], $jsonrpc)->andReturn($request);

        $this->assertSame($request, $this->client->notification('foo', ['bar'=>true]));
    }

    public function testRequest()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo', 'id'=>123];
        $type = RequestInterface::REQUEST;
        $uri = 'http://foo';

        $this->httpClient->shouldReceive('getConfig')->once()->with('base_uri')->andReturn($uri);
        $this->httpClient->shouldReceive('getConfig')->once()->with('defaults')->andReturn([]);
        $this->messageFactory->shouldReceive('createRequest')->once()->with($type, $uri, [], $jsonrpc)->andReturn($request);

        $this->assertSame($request, $this->client->request(123, 'foo'));
    }

    public function testRequestWithParams()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo', 'params'=>['bar'=>true], 'id'=>123];
        $type = RequestInterface::REQUEST;
        $uri = 'http://foo';

        $this->httpClient->shouldReceive('getConfig')->once()->with('base_uri')->andReturn($uri);
        $this->httpClient->shouldReceive('getConfig')->once()->with('defaults')->andReturn([]);
        $this->messageFactory->shouldReceive('createRequest')->once()->with($type, $uri, [], $jsonrpc)->andReturn($request);

        $this->assertSame($request, $this->client->request(123, 'foo', ['bar'=>true]));
    }

    public function testRequestWithEmptyParams()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo', 'id'=>123];
        $type = RequestInterface::REQUEST;
        $uri = 'http://foo';

        $this->httpClient->shouldReceive('getConfig')->once()->with('base_uri')->andReturn($uri);
        $this->httpClient->shouldReceive('getConfig')->once()->with('defaults')->andReturn([]);
        $this->messageFactory->shouldReceive('createRequest')->once()->with($type, $uri, [], $jsonrpc)->andReturn($request);

        $this->assertSame($request, $this->client->request(123, 'foo', []));
    }

    public function testSendNotification()
    {
        $request = $this->mockRequest();
        $response = $this->mockResponse();

        $request->shouldReceive('getRpcId')->once()->withNoArgs()->andReturn(null);
        $this->httpClient->shouldReceive('send')->once()->with($request)->andReturn($response);

        $this->assertNull($this->client->send($request));
    }

    public function testSendRequest()
    {
        $request = $this->mockRequest();
        $response = $this->mockResponse();

        $request->shouldReceive('getRpcId')->once()->withNoArgs()->andReturn('foo');
        $this->httpClient->shouldReceive('send')->once()->with($request)->andReturn($response);

        $this->assertSame($response, $this->client->send($request));
    }

    public function testSendAll()
    {
        $batchRequest = $this->mockRequest();
        $requestA = $this->mockRequest();
        $requestB = $this->mockRequest();
        $batchResponse = $this->mockResponse();
        $responseA = $this->mockResponse();
        $responseB = $this->mockResponse();

        $factory = $this->mockMessageFactory();
        $this->httpClient->messageFactory = $factory;

        $requestA->shouldReceive('getBody')->once()->withNoArgs()->andReturn('["foo"]');
        $requestB->shouldReceive('getBody')->once()->withNoArgs()->andReturn('["bar"]');

        $type = RequestInterface::BATCH;
        $uri = 'http://foo';
        $this->messageFactory->shouldReceive('createRequest')->once()->with($type, $uri, [], [['foo'], ['bar']])->andReturn($batchRequest);
        $this->httpClient->shouldReceive('getConfig')->once()->with('base_uri')->andReturn($uri);
        $this->httpClient->shouldReceive('getConfig')->once()->with('defaults')->andReturn([]);
        $this->httpClient->shouldReceive('send')->once()->with($batchRequest)->andReturn($batchResponse);

        $batchResponse->shouldReceive('getBody')->once()->withNoArgs()->andReturn('[["foo"], ["bar"]]');
        $batchResponse->shouldReceive('getStatusCode')->times(2)->withNoArgs()->andReturn(200);
        $batchResponse->shouldReceive('getHeaders')->times(2)->withNoArgs()->andReturn(['headers']);

        $this->messageFactory->shouldReceive('createResponse')->once()->with(200, ['headers'], ['foo'])->andReturn($responseA);
        $this->messageFactory->shouldReceive('createResponse')->once()->with(200, ['headers'], ['bar'])->andReturn($responseB);

        $this->assertSame([$responseA, $responseB], $this->client->sendAll([$requestA, $requestB]));
    }
}
