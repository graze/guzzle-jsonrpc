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
namespace Graze\GuzzleHttp\JsonRpc\Message;

use Graze\GuzzleHttp\JsonRpc\ClientInterface;
use Graze\GuzzleHttp\JsonRpc\Test\UnitTestCase;

class MessageFactoryTest extends UnitTestCase
{
    public function setup()
    {
        $this->factory = new MessageFactory();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('GuzzleHttp\Message\MessageFactoryInterface', $this->factory);
    }

    public function testCreateRequest()
    {
        $method = RequestInterface::REQUEST;
        $url = 'http://bar';
        $options = [
            'jsonrpc' => [
                'method' => 'baz'
            ]
        ];

        $request = $this->factory->createRequest($method, $url, $options);
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\RequestInterface', $request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('http://bar', $request->getUrl());
        $this->assertEquals('baz', $request->getRpcMethod());
    }

    public function testCreateResponse()
    {
        $status = 200;
        $headers = ['Content-Type'=>'application/json'];

        $response = $this->factory->createResponse($status, $headers);
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface', $response);
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertEquals(null, $response->getBody());
        $this->assertEquals(null, $response->getRpcVersion());
        $this->assertEquals(null, $response->getRpcResult());
        $this->assertEquals(null, $response->getRpcId());
        $this->assertEquals(null, $response->getRpcErrorCode());
        $this->assertEquals(null, $response->getRpcErrorMessage());
    }

    public function testCreateResponseWithBody()
    {
        $status = 200;
        $headers = ['Content-Type'=>'application/json'];
        $body = json_encode([
            'jsonrpc' => ClientInterface::SPEC,
            'result' => 'foo',
            'id' => 123
        ]);

        $response = $this->factory->createResponse($status, $headers, $body);
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface', $response);
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $this->assertEquals($body, (string) $response->getBody());
        $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
        $this->assertEquals('foo', $response->getRpcResult());
        $this->assertEquals(123, $response->getRpcId());
        $this->assertEquals(null, $response->getRpcErrorCode());
        $this->assertEquals(null, $response->getRpcErrorMessage());
    }
}
