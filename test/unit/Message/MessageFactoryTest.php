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
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\MessageFactoryInterface', $this->factory);
    }

    public function testCreateRequest()
    {
        $method = RequestInterface::REQUEST;
        $uri = 'http://bar';
        $options = [
            'method' => 'baz'
        ];

        $request = $this->factory->createRequest($method, $uri, [], $options);
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\RequestInterface', $request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('http://bar', (string) $request->getUri());
        $this->assertEquals('baz', $request->getRpcMethod());
    }

    public function testCreateResponse()
    {
        $status = 200;
        $headers = ['Content-Type'=>'application/json'];

        $response = $this->factory->createResponse($status, $headers);
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface', $response);
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('[]', (string) $response->getBody());
        $this->assertEquals(null, $response->getRpcVersion());
        $this->assertEquals(null, $response->getRpcResult());
        $this->assertEquals(null, $response->getRpcId());
        $this->assertEquals(null, $response->getRpcErrorCode());
        $this->assertEquals(null, $response->getRpcErrorMessage());
    }

    public function testCreateResponseWithOptions()
    {
        $status = 200;
        $headers = ['Content-Type'=>'application/json'];
        $options = [
            'jsonrpc' => ClientInterface::SPEC,
            'result' => 'foo',
            'id' => 123
        ];

        $response = $this->factory->createResponse($status, $headers, $options);
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface', $response);
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals(json_encode($options), (string) $response->getBody());
        $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
        $this->assertEquals('foo', $response->getRpcResult());
        $this->assertEquals(123, $response->getRpcId());
        $this->assertEquals(null, $response->getRpcErrorCode());
        $this->assertEquals(null, $response->getRpcErrorMessage());
    }
}
