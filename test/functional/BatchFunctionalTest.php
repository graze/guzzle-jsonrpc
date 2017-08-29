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

use Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface;
use Graze\GuzzleHttp\JsonRpc\Test\FunctionalTestCase;

class BatchFunctionalTest extends FunctionalTestCase
{
    /** @var Client */
    private $client;

    public function setUp()
    {
        $this->client = $this->createClient();
    }

    public function testBatchRequestWithOneChild()
    {
        $id = 'abc';
        $method = 'sum';
        $params = ['foo'=>123, 'bar'=>456];
        $request = $this->client->request($id, $method, $params);
        $responses = $this->client->sendAll([$request]);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals($id, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals($params, $request->getRpcParams());

        $this->assertTrue(is_array($responses));
        $this->assertEquals(ClientInterface::SPEC, $responses[0]->getRpcVersion());
        $this->assertEquals(array_sum($params), $responses[0]->getRpcResult());
        $this->assertEquals($id, $responses[0]->getRpcId());
        $this->assertEquals(null, $responses[0]->getRpcErrorCode());
        $this->assertEquals(null, $responses[0]->getRpcErrorMessage());
    }

    public function testAsyncBatchRequestWithOneChild()
    {
        $id = 'abc';
        $method = 'sum';
        $params = ['foo' => 123, 'bar' => 456];
        $request = $this->client->request($id, $method, $params);
        $promise = $this->client->sendAllAsync([$request]);

        $promise->then(function ($responses) use ($request, $id, $method, $params) {
            $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
            $this->assertEquals($id, $request->getRpcId());
            $this->assertEquals($method, $request->getRpcMethod());
            $this->assertEquals($params, $request->getRpcParams());

            $this->assertTrue(is_array($responses));
            $this->assertEquals(ClientInterface::SPEC, $responses[0]->getRpcVersion());
            $this->assertEquals(array_sum($params), $responses[0]->getRpcResult());
            $this->assertEquals($id, $responses[0]->getRpcId());
            $this->assertEquals(null, $responses[0]->getRpcErrorCode());
            $this->assertEquals(null, $responses[0]->getRpcErrorMessage());
        })->wait();
    }

    /**
     * @param ResponseInterface[] $responses
     * @param string|int          $id
     *
     * @return ResponseInterface
     */
    private function getResponseFromArray(array $responses, $id)
    {
        $filtered = array_values(array_filter($responses, function (ResponseInterface $response) use ($id) {
            return $response->getRpcId() == $id;
        }));

        if (count($filtered) === 0) {
            $this->fail('Unable to find response with id:' . $id);
        }
        return reset($filtered);
    }

    public function testBatchRequestWithMultipleChildren()
    {
        $idA = 123;
        $idC = 'abc';
        $idD = 'def';
        $methodA = 'concat';
        $methodB = 'nofify';
        $methodC = 'sum';
        $methodD = 'bar';
        $paramsA = ['foo'=>'abc', 'bar'=>'def'];
        $paramsB = ['foo'=>false];
        $paramsC = ['foo'=>123, 'bar'=>456];
        $paramsD = ['foo'=>123, 'bar'=>456];
        $requestA = $this->client->request($idA, $methodA, $paramsA);
        $requestB = $this->client->notification($methodB, $paramsB);
        $requestC = $this->client->request($idC, $methodC, $paramsC);
        $requestD = $this->client->request($idD, $methodD, $paramsD);
        $responses = $this->client->sendAll([$requestA, $requestB, $requestC, $requestD]);

        $this->assertEquals(ClientInterface::SPEC, $requestA->getRpcVersion());
        $this->assertEquals($idA, $requestA->getRpcId());
        $this->assertEquals($methodA, $requestA->getRpcMethod());
        $this->assertEquals($paramsA, $requestA->getRpcParams());
        $this->assertEquals(ClientInterface::SPEC, $requestB->getRpcVersion());
        $this->assertEquals(null, $requestB->getRpcId());
        $this->assertEquals($methodB, $requestB->getRpcMethod());
        $this->assertEquals($paramsB, $requestB->getRpcParams());
        $this->assertEquals(ClientInterface::SPEC, $requestC->getRpcVersion());
        $this->assertEquals($idC, $requestC->getRpcId());
        $this->assertEquals($methodC, $requestC->getRpcMethod());
        $this->assertEquals($paramsC, $requestC->getRpcParams());
        $this->assertEquals(ClientInterface::SPEC, $requestD->getRpcVersion());
        $this->assertEquals($idD, $requestD->getRpcId());
        $this->assertEquals($methodD, $requestD->getRpcMethod());
        $this->assertEquals($paramsD, $requestD->getRpcParams());

        $this->assertTrue(is_array($responses));
        $this->assertEquals(3, count($responses));

        $responseA = $this->getResponseFromArray($responses, $idA);
        $responseC = $this->getResponseFromArray($responses, $idC);
        $responseD = $this->getResponseFromArray($responses, $idD);

        $this->assertEquals(ClientInterface::SPEC, $responseA->getRpcVersion());
        $this->assertEquals(implode('', $paramsA), $responseA->getRpcResult());
        $this->assertEquals($idA, $responseA->getRpcId());
        $this->assertEquals(null, $responseA->getRpcErrorCode());
        $this->assertEquals(null, $responseA->getRpcErrorMessage());
        $this->assertEquals(ClientInterface::SPEC, $responseC->getRpcVersion());
        $this->assertEquals(array_sum($paramsC), $responseC->getRpcResult());
        $this->assertEquals($idC, $responseC->getRpcId());
        $this->assertEquals(null, $responseC->getRpcErrorCode());
        $this->assertEquals(null, $responseC->getRpcErrorMessage());
        $this->assertEquals(ClientInterface::SPEC, $responseD->getRpcVersion());
        $this->assertEquals(null, $responseD->getRpcResult());
        $this->assertEquals($idD, $responseD->getRpcId());
        $this->assertTrue(is_int($responseD->getRpcErrorCode()));
        $this->assertTrue(is_string($responseD->getRpcErrorMessage()));
    }

    public function testAsyncBatchRequestWithMultipleChildren()
    {
        $idA = 123;
        $idC = 'abc';
        $idD = 'def';
        $methodA = 'concat';
        $methodB = 'nofify';
        $methodC = 'sum';
        $methodD = 'bar';
        $paramsA = ['foo'=>'abc', 'bar'=>'def'];
        $paramsB = ['foo'=>false];
        $paramsC = ['foo'=>123, 'bar'=>456];
        $paramsD = ['foo'=>123, 'bar'=>456];
        $requestA = $this->client->request($idA, $methodA, $paramsA);
        $requestB = $this->client->notification($methodB, $paramsB);
        $requestC = $this->client->request($idC, $methodC, $paramsC);
        $requestD = $this->client->request($idD, $methodD, $paramsD);
        $promise = $this->client->sendAllAsync([$requestA, $requestB, $requestC, $requestD]);

        $promise->then(function ($responses) use ($requestA, $requestB, $requestC, $requestD, $idA, $idC, $idD, $methodA, $methodB, $methodC, $methodD, $paramsA, $paramsB, $paramsC, $paramsD) {
            $this->assertEquals(ClientInterface::SPEC, $requestA->getRpcVersion());
            $this->assertEquals($idA, $requestA->getRpcId());
            $this->assertEquals($methodA, $requestA->getRpcMethod());
            $this->assertEquals($paramsA, $requestA->getRpcParams());
            $this->assertEquals(ClientInterface::SPEC, $requestB->getRpcVersion());
            $this->assertEquals(null, $requestB->getRpcId());
            $this->assertEquals($methodB, $requestB->getRpcMethod());
            $this->assertEquals($paramsB, $requestB->getRpcParams());
            $this->assertEquals(ClientInterface::SPEC, $requestC->getRpcVersion());
            $this->assertEquals($idC, $requestC->getRpcId());
            $this->assertEquals($methodC, $requestC->getRpcMethod());
            $this->assertEquals($paramsC, $requestC->getRpcParams());
            $this->assertEquals(ClientInterface::SPEC, $requestD->getRpcVersion());
            $this->assertEquals($idD, $requestD->getRpcId());
            $this->assertEquals($methodD, $requestD->getRpcMethod());
            $this->assertEquals($paramsD, $requestD->getRpcParams());

            $this->assertTrue(is_array($responses));
            $this->assertEquals(3, count($responses));

            $responseA = $this->getResponseFromArray($responses, $idA);
            $responseC = $this->getResponseFromArray($responses, $idC);
            $responseD = $this->getResponseFromArray($responses, $idD);

            $this->assertEquals(ClientInterface::SPEC, $responseA->getRpcVersion());
            $this->assertEquals(implode('', $paramsA), $responseA->getRpcResult());
            $this->assertEquals($idA, $responseA->getRpcId());
            $this->assertEquals(null, $responseA->getRpcErrorCode());
            $this->assertEquals(null, $responseA->getRpcErrorMessage());
            $this->assertEquals(ClientInterface::SPEC, $responseC->getRpcVersion());
            $this->assertEquals(array_sum($paramsC), $responseC->getRpcResult());
            $this->assertEquals($idC, $responseC->getRpcId());
            $this->assertEquals(null, $responseC->getRpcErrorCode());
            $this->assertEquals(null, $responseC->getRpcErrorMessage());
            $this->assertEquals(ClientInterface::SPEC, $responseD->getRpcVersion());
            $this->assertEquals(null, $responseD->getRpcResult());
            $this->assertEquals($idD, $responseD->getRpcId());
            $this->assertTrue(is_int($responseD->getRpcErrorCode()));
            $this->assertTrue(is_string($responseD->getRpcErrorMessage()));
        })->wait();
    }
}
