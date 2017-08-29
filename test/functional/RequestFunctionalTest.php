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

use Graze\GuzzleHttp\JsonRpc\Exception\ClientException;
use Graze\GuzzleHttp\JsonRpc\Test\FunctionalTestCase;
use GuzzleHttp\Promise\PromiseInterface;

class RequestFunctionalTest extends FunctionalTestCase
{
    /** @var Client */
    private $client;

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
        $this->assertEquals(null, $response->getRpcErrorData());
    }

    public function testConcatAsyncRequest()
    {
        $id = 123;
        $method = 'concat';
        $params = ['foo'=>'abc', 'bar'=>'def'];
        $request = $this->client->request($id, $method, $params);
        $promise = $this->client->sendAsync($request);

        $promise->then(function ($response) use ($request, $id, $method, $params) {
            $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
            $this->assertEquals($id, $request->getRpcId());
            $this->assertEquals($method, $request->getRpcMethod());
            $this->assertEquals($params, $request->getRpcParams());

            $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
            $this->assertEquals(implode('', $params), $response->getRpcResult());
            $this->assertEquals($id, $response->getRpcId());
            $this->assertEquals(null, $response->getRpcErrorCode());
            $this->assertEquals(null, $response->getRpcErrorMessage());
            $this->assertEquals(null, $response->getRpcErrorData());
        })->wait();
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
        $this->assertEquals(null, $response->getRpcErrorData());
    }

    public function testSumAsyncRequest()
    {
        $id = 'abc';
        $method = 'sum';
        $params = ['foo'=>123, 'bar'=>456];
        $request = $this->client->request($id, $method, $params);
        $promise = $this->client->sendAsync($request);

        $promise->then(function ($response) use ($request, $id, $method, $params) {
            $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
            $this->assertEquals($id, $request->getRpcId());
            $this->assertEquals($method, $request->getRpcMethod());
            $this->assertEquals($params, $request->getRpcParams());

            $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
            $this->assertEquals(array_sum($params), $response->getRpcResult());
            $this->assertEquals($id, $response->getRpcId());
            $this->assertEquals(null, $response->getRpcErrorCode());
            $this->assertEquals(null, $response->getRpcErrorMessage());
            $this->assertEquals(null, $response->getRpcErrorData());
        })->wait();
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
        $this->assertEquals(null, $response->getRpcErrorData());
    }

    public function testFooAsyncRequest()
    {
        $id = 'abc';
        $method = 'foo';
        $request = $this->client->request($id, $method, []);
        $promise = $this->client->sendAsync($request);

        $promise->then(function ($response) use ($request, $id, $method) {
            $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
            $this->assertEquals($id, $request->getRpcId());
            $this->assertEquals($method, $request->getRpcMethod());
            $this->assertEquals(null, $request->getRpcParams());

            $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
            $this->assertEquals('foo', $response->getRpcResult());
            $this->assertEquals($id, $response->getRpcId());
            $this->assertEquals(null, $response->getRpcErrorCode());
            $this->assertEquals(null, $response->getRpcErrorMessage());
            $this->assertEquals(null, $response->getRpcErrorData());
        })->wait();
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
        $this->assertEquals(null, $response->getRpcErrorData());
    }

    public function testBarAsyncRequest()
    {
        $id = 'abc';
        $method = 'bar';
        $request = $this->client->request($id, $method, []);
        $promise = $this->client->sendAsync($request);

        $promise->then(function ($response) use ($request, $id, $method) {
            $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
            $this->assertEquals($id, $request->getRpcId());
            $this->assertEquals($method, $request->getRpcMethod());
            $this->assertEquals(null, $request->getRpcParams());

            $this->assertEquals(ClientInterface::SPEC, $response->getRpcVersion());
            $this->assertEquals(null, $response->getRpcResult());
            $this->assertEquals($id, $response->getRpcId());
            $this->assertTrue(is_int($response->getRpcErrorCode()));
            $this->assertTrue(is_string($response->getRpcErrorMessage()));
            $this->assertEquals(null, $response->getRpcErrorData());
        })->wait();
    }

    /**
     * @expectedException \Graze\GuzzleHttp\JsonRpc\Exception\ClientException
     */
    public function testBarRequestThrows()
    {
        $id = 'abc';
        $method = 'bar';
        $client = $this->createClient(null, ['rpc_error' => true]);
        $request = $client->request($id, $method, []);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals($id, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals(null, $request->getRpcParams());

        $client->send($request);
    }

    public function testBarAsyncRequestIsRejected()
    {
        $id = 'abc';
        $method = 'bar';
        $client = $this->createClient(null, ['rpc_error' => true]);
        $request = $client->request($id, $method, []);
        $promise = $client->sendAsync($request);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals($id, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals(null, $request->getRpcParams());

        $promise->then(function () use ($request, $id, $method) {
            $this->fail('This promise should not be fulfilled');
        }, function ($reason) {
            $this->assertInstanceOf(ClientException::class, $reason);
        })->wait();
    }
}
