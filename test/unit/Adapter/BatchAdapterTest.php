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
namespace Graze\GuzzleHttp\JsonRpc\Adapter;

use ArrayIterator;
use Graze\GuzzleHttp\JsonRpc\ClientInterface;
use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Test\UnitTestCase;
use Mockery;

class BatchAdapterTest extends UnitTestCase
{
    public function setup()
    {
        $this->factory = $this->mockMessageFactory();
        $this->adapter = new BatchAdapter($this->factory);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('GuzzleHttp\Adapter\ParallelAdapterInterface', $this->adapter);
    }

    public function testSendAll()
    {
        $url = 'http://foo';
        $client = $this->mockHttpClient();
        $batchRequest = $this->mockRequest();
        $transactions = new ArrayIterator([
            $transactionA = $this->mockTransaction(),
            $transactionB = $this->mockTransaction(),
            $transactionC = $this->mockTransaction()
        ]);

        $requestA = $this->mockRequest();
        $requestB = $this->mockRequest();
        $requestC = $this->mockRequest();
        $requestA->shouldReceive('getBody')->once()->withNoArgs()->andReturn(json_encode('foo'));
        $requestB->shouldReceive('getBody')->once()->withNoArgs()->andReturn(json_encode('bar'));
        $requestC->shouldReceive('getBody')->once()->withNoArgs()->andReturn(json_encode('baz'));

        $transactionA->shouldReceive('getClient')->once()->withNoArgs()->andReturn($client);
        $transactionA->shouldReceive('getRequest')->once()->withNoArgs()->andReturn($requestA);
        $transactionB->shouldReceive('getClient')->once()->withNoArgs()->andReturn($client);
        $transactionB->shouldReceive('getRequest')->once()->withNoArgs()->andReturn($requestB);
        $transactionC->shouldReceive('getClient')->once()->withNoArgs()->andReturn($client);
        $transactionC->shouldReceive('getRequest')->once()->withNoArgs()->andReturn($requestC);

        $type = RequestInterface::BATCH;
        $params = ['foo', 'bar', 'baz'];

        $this->factory->shouldReceive('createRequest')->once()->with($type, $url, ['jsonrpc'=>$params])->andReturn($batchRequest);
        $batchRequest->shouldReceive('getEmitter->attach')->once()->with(Mockery::type('Graze\GuzzleHttp\JsonRpc\Subscriber\BatchSubscriber'));
        $client->shouldReceive('getBaseUrl')->once()->withNoArgs()->andReturn($url);
        $client->shouldReceive('send')->once()->with($batchRequest);

        $this->adapter->sendAll($transactions, 100);
    }

    public function testSendAllWithLimit()
    {
        $url = 'http://foo';
        $client = $this->mockHttpClient();
        $batchRequest = $this->mockRequest();
        $transactions = new ArrayIterator([
            $transactionA = $this->mockTransaction(),
            $transactionB = $this->mockTransaction(),
            $transactionC = $this->mockTransaction()
        ]);

        $requestA = $this->mockRequest();
        $requestA->shouldReceive('getBody')->once()->withNoArgs()->andReturn(json_encode('foo'));

        $transactionA->shouldReceive('getClient')->once()->withNoArgs()->andReturn($client);
        $transactionA->shouldReceive('getRequest')->once()->withNoArgs()->andReturn($requestA);

        $type = RequestInterface::BATCH;
        $params = ['foo'];

        $this->factory->shouldReceive('createRequest')->once()->with($type, $url, ['jsonrpc'=>$params])->andReturn($batchRequest);
        $batchRequest->shouldReceive('getEmitter->attach')->once()->with(Mockery::type('Graze\GuzzleHttp\JsonRpc\Subscriber\BatchSubscriber'));
        $client->shouldReceive('getBaseUrl')->once()->withNoArgs()->andReturn($url);
        $client->shouldReceive('send')->once()->with($batchRequest);

        $this->adapter->sendAll($transactions, 1);
    }

    public function testSendAllWithMismatchingClients()
    {
        $transactions = new ArrayIterator([
            $transactionA = $this->mockTransaction(),
            $transactionB = $this->mockTransaction(),
            $transactionC = $this->mockTransaction()
        ]);

        $transactionA->shouldReceive('getClient')->once()->withNoArgs()->andReturn($this->mockHttpClient());
        $transactionB->shouldReceive('getClient')->once()->withNoArgs()->andReturn($this->mockHttpClient());

        $this->setExpectedException('LogicException');

        $this->adapter->sendAll($transactions, 100);
    }
}
