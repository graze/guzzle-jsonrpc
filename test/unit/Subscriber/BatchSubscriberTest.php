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
namespace Graze\GuzzleHttp\JsonRpc\Subscriber;

use ArrayIterator;
use Exception;
use Graze\GuzzleHttp\JsonRpc\Test\UnitTestCase;
use GuzzleHttp\Exception\RequestException;
use Mockery;

class BatchSubscriberTest extends UnitTestCase
{
    public function setUp()
    {
        $this->requestA = $this->mockRequest();
        $this->requestB = $this->mockRequest();
        $this->requestC = $this->mockRequest();

        $this->transactions = new ArrayIterator([
            $this->transactionA = $this->mockTransaction(),
            $this->transactionB = $this->mockTransaction(),
            $this->transactionC = $this->mockTransaction()
        ]);

        $this->subscriber = new BatchSubscriber($this->transactions);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('GuzzleHttp\Event\SubscriberInterface', $this->subscriber);
    }

    public function testGetEvents()
    {
        $this->assertEquals([
            'before'   => ['onBefore'],
            'complete' => ['onComplete'],
            'error'    => ['onError']
        ], $this->subscriber->getEvents());
    }

    public function testOnBefore()
    {
        $event = $this->mockBeforeEvent();

        $this->transactionA->shouldReceive('getRequest')->once()->withNoArgs()->andReturn($this->requestA);
        $this->transactionB->shouldReceive('getRequest')->once()->withNoArgs()->andReturn($this->requestB);
        $this->transactionC->shouldReceive('getRequest')->once()->withNoArgs()->andReturn($this->requestC);

        $this->requestA->shouldReceive('getEmitter->emit')->once()->with('before', Mockery::type('GuzzleHttp\Event\BeforeEvent'));
        $this->requestB->shouldReceive('getEmitter->emit')->once()->with('before', Mockery::type('GuzzleHttp\Event\BeforeEvent'));
        $this->requestC->shouldReceive('getEmitter->emit')->once()->with('before', Mockery::type('GuzzleHttp\Event\BeforeEvent'));

        $this->subscriber->onBefore($event);
    }

    public function testOnComplete()
    {
        $data = [
            ['id'=>1, 'result'=>'foo'],
            ['id'=>2, 'result'=>'bar'],
            ['id'=>3, 'result'=>'baz']
        ];

        $response = $this->mockResponse();
        $response->shouldReceive('json')->once()->withNoArgs()->andReturn($data);
        $response->shouldReceive('setBody')->times(3)->with(Mockery::type('GuzzleHttp\Stream\StreamInterface'));

        $responseA = $this->mockResponse();
        $responseB = $this->mockResponse();
        $responseC = $this->mockResponse();

        $event = $this->mockCompleteEvent();
        $event->shouldReceive('getResponse')->once()->withNoArgs()->andReturn($response);

        $this->transactionA->shouldReceive('getRequest')->times(2)->withNoArgs()->andReturn($this->requestA);
        $this->transactionA->shouldReceive('getResponse')->once()->withNoArgs()->andReturn($responseA);
        $this->transactionA->shouldReceive('setResponse')->once()->with(Mockery::type('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface'));
        $this->transactionB->shouldReceive('getRequest')->times(2)->withNoArgs()->andReturn($this->requestB);
        $this->transactionB->shouldReceive('getResponse')->once()->withNoArgs()->andReturn($responseB);
        $this->transactionB->shouldReceive('setResponse')->once()->with(Mockery::type('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface'));
        $this->transactionC->shouldReceive('getRequest')->times(2)->withNoArgs()->andReturn($this->requestC);
        $this->transactionC->shouldReceive('getResponse')->once()->withNoArgs()->andReturn($responseC);
        $this->transactionC->shouldReceive('setResponse')->once()->with(Mockery::type('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface'));

        $this->requestA->shouldReceive('getEmitter->emit')->once()->with('complete', Mockery::type('GuzzleHttp\Event\CompleteEvent'));
        $this->requestB->shouldReceive('getEmitter->emit')->once()->with('complete', Mockery::type('GuzzleHttp\Event\CompleteEvent'));
        $this->requestC->shouldReceive('getEmitter->emit')->once()->with('complete', Mockery::type('GuzzleHttp\Event\CompleteEvent'));

        $this->requestA->shouldReceive('getRpcId')->times(3)->withNoArgs()->andReturn(1);
        $this->requestB->shouldReceive('getRpcId')->times(3)->withNoArgs()->andReturn(2);
        $this->requestC->shouldReceive('getRpcId')->times(3)->withNoArgs()->andReturn(3);

        $this->requestA->shouldReceive('getUrl')->once()->withNoArgs()->andReturn('http://foo');
        $this->requestB->shouldReceive('getUrl')->once()->withNoArgs()->andReturn('http://foo');
        $this->requestC->shouldReceive('getUrl')->once()->withNoArgs()->andReturn('http://foo');

        $responseA->shouldReceive('setEffectiveUrl')->once()->with('http://foo');
        $responseB->shouldReceive('setEffectiveUrl')->once()->with('http://foo');
        $responseC->shouldReceive('setEffectiveUrl')->once()->with('http://foo');

        $this->subscriber->onComplete($event);
    }
}
