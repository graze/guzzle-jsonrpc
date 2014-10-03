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

use Graze\GuzzleHttp\JsonRpc\Test\FunctionalTestCase;

class RequestFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        $this->client = $this->createClient();
    }

    public function testConcatRequest()
    {
        $id = 123;
        $method = 'concat';
        $params = ['foo'=>'abc', 'bar'=>'def'];
        $request = $this->client->request($id, $method, $params);
        $response = $this->client->send($request);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals($id, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals($params, $request->getRpcParams());

        $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
        $this->assertEquals(implode('', $params), $response->getRpcResult());
        $this->assertEquals($id, $response->getRpcId());
        $this->assertEquals(null, $response->getRpcErrorCode());
        $this->assertEquals(null, $response->getRpcErrorMessage());
    }

    public function testSumRequest()
    {
        $id = 'abc';
        $method = 'sum';
        $params = ['foo'=>123, 'bar'=>456];
        $request = $this->client->request($id, $method, $params);
        $response = $this->client->send($request);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals($id, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals($params, $request->getRpcParams());

        $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
        $this->assertEquals(array_sum($params), $response->getRpcResult());
        $this->assertEquals($id, $response->getRpcId());
        $this->assertEquals(null, $response->getRpcErrorCode());
        $this->assertEquals(null, $response->getRpcErrorMessage());
    }

    public function testFooRequest()
    {
        $id = 'abc';
        $method = 'foo';
        $request = $this->client->request($id, $method, []);
        $response = $this->client->send($request);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals($id, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals(null, $request->getRpcParams());

        $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
        $this->assertEquals('foo', $response->getRpcResult());
        $this->assertEquals($id, $response->getRpcId());
        $this->assertEquals(null, $response->getRpcErrorCode());
        $this->assertEquals(null, $response->getRpcErrorMessage());
    }

    public function testBarRequest()
    {
        $id = 'abc';
        $method = 'bar';
        $request = $this->client->request($id, $method, []);
        $response = $this->client->send($request);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals($id, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals(null, $request->getRpcParams());

        $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
        $this->assertEquals(null, $response->getRpcResult());
        $this->assertEquals($id, $response->getRpcId());
        $this->assertTrue(is_int($response->getRpcErrorCode()));
        $this->assertTrue(is_string($response->getRpcErrorMessage()));
    }
}
