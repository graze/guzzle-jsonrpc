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

class RequestTest extends UnitTestCase
{
    public function setUp()
    {
        $this->stream = $this->mockStream();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Message\RequestInterface', new Request('foo', 'bar'));
        $this->assertInstanceOf('Psr\Http\Message\RequestInterface', new Request('foo', 'bar'));
    }

    public function testGetRpcId()
    {
        $request = new Request('foo', 'bar', [], json_encode([
            'id' => 123
        ]));

        $this->assertEquals(123, $request->getRpcId());
    }

    public function testGetRpcIdIsNull()
    {
        $request = new Request('foo', 'bar');

        $this->assertNull($request->getRpcId());
    }

    public function testGetRpcMethod()
    {
        $request = new Request('foo', 'bar', [], json_encode([
            'method' => 'foo'
        ]));

        $this->assertEquals('foo', $request->getRpcMethod());
    }

    public function testGetRpcMethodIsNull()
    {
        $request = new Request('foo', 'bar');

        $this->assertNull($request->getRpcMethod());
    }

    public function testGetRpcParams()
    {
        $request = new Request('foo', 'bar', [], json_encode([
            'params' => ['foo'=>'bar']
        ]));

        $this->assertEquals(['foo'=>'bar'], $request->getRpcParams());
    }

    public function testGetRpcParamsIsNull()
    {
        $request = new Request('foo', 'bar');

        $this->assertNull($request->getRpcParams());
    }

    public function testGetRpcVersion()
    {
        $request = new Request('foo', 'bar', [], json_encode([
            'jsonrpc' => 'foo'
        ]));

        $this->assertEquals('foo', $request->getRpcVersion());
    }

    public function testGetRpcVersionIsNull()
    {
        $request = new Request('foo', 'bar');

        $this->assertNull($request->getRpcVersion());
    }
}
