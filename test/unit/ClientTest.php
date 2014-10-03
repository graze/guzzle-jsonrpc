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
        $this->client = new Client($this->httpClient);
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

        $this->httpClient->shouldReceive('createRequest')->once()->with($type, null, ['jsonrpc'=>$jsonrpc])->andReturn($request);

        $this->assertSame($request, $this->client->notification('foo'));
    }

    public function testNotificationWithParams()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo', 'params'=>['bar'=>true]];
        $type = RequestInterface::NOTIFICATION;

        $this->httpClient->shouldReceive('createRequest')->once()->with($type, null, ['jsonrpc'=>$jsonrpc])->andReturn($request);

        $this->assertSame($request, $this->client->notification('foo', ['bar'=>true]));
    }

    public function testRequest()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo', 'id'=>123];
        $type = RequestInterface::REQUEST;

        $this->httpClient->shouldReceive('createRequest')->once()->with($type, null, ['jsonrpc'=>$jsonrpc])->andReturn($request);

        $this->assertSame($request, $this->client->request(123, 'foo'));
    }

    public function testRequestWithParams()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo', 'params'=>['bar'=>true], 'id'=>123];
        $type = RequestInterface::REQUEST;

        $this->httpClient->shouldReceive('createRequest')->once()->with($type, null, ['jsonrpc'=>$jsonrpc])->andReturn($request);

        $this->assertSame($request, $this->client->request(123, 'foo', ['bar'=>true]));
    }

    public function testRequestWithEmptyParams()
    {
        $request = $this->mockRequest();
        $jsonrpc = ['jsonrpc'=>ClientInterface::SPEC, 'method'=>'foo', 'id'=>123];
        $type = RequestInterface::REQUEST;

        $this->httpClient->shouldReceive('createRequest')->once()->with($type, null, ['jsonrpc'=>$jsonrpc])->andReturn($request);

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
        $requestA = $this->mockRequest();
        $requestB = $this->mockRequest();

        $this->httpClient->shouldReceive('sendAll')->once()->with([$requestA, $requestB], Mockery::on(function ($listeners) {
            $before = isset($listeners['before']) && is_callable($listeners['before']);
            $complete = isset($listeners['complete']) && is_callable($listeners['complete']);

            return $before && $complete;
        }));

        $this->client->sendAll([$requestA, $requestB]);
    }
}
