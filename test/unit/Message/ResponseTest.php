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

class ResponseTest extends UnitTestCase
{
    public function setUp()
    {
        $this->stream = $this->mockStream();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface', new Response(200));
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', new Response(200));
    }

    public function testGetRpcId()
    {
        $response = new Response(200, [], json_encode([
            'id' => 123
        ]));

        $this->assertEquals(123, $response->getRpcId());
    }

    public function testGetRpcIdIsNull()
    {
        $response = new Response(200);

        $this->assertNull($response->getRpcId());
    }

    public function testGetRpcErrorCode()
    {
        $response = new Response(200, [], json_encode([
            'error' => ['code'=>123]
        ]));

        $this->assertEquals(123, $response->getRpcErrorCode());
    }

    public function testGetRpcErrorCodeIsNull()
    {
        $response = new Response(200);

        $this->assertNull($response->getRpcErrorCode());
    }

    public function testGetRpcErrorMessage()
    {
        $response = new Response(200, [], json_encode([
            'error' => ['message'=>'foo']
        ]));

        $this->assertEquals('foo', $response->getRpcErrorMessage());
    }

    public function testGetRpcErrorMessageIsNull()
    {
        $response = new Response(200);

        $this->assertNull($response->getRpcErrorMessage());
    }

    public function testGetRpcErrorData()
    {
        $response = new Response(200, [], json_encode([
            'error' => ['data' => array()]
        ]));

        $this->assertEquals(array(), $response->getRpcErrorData());
    }

    public function testGetRpcErrorDataIsNull()
    {
        $response = new Response(200);

        $this->assertNull($response->getRpcErrorData());
    }

    public function testGetRpcResult()
    {
        $response = new Response(200, [], json_encode([
            'result' => 'foo'
        ]));

        $this->assertEquals('foo', $response->getRpcResult());
    }

    public function testGetRpcResultIsNull()
    {
        $response = new Response(200);

        $this->assertNull($response->getRpcResult());
    }

    public function testGetRpcVersion()
    {
        $response = new Response(200, [], json_encode([
            'jsonrpc' => 'foo'
        ]));

        $this->assertEquals('foo', $response->getRpcVersion());
    }

    public function testGetRpcVersionIsNull()
    {
        $response = new Response(200);

        $this->assertNull($response->getRpcVersion());
    }
}
