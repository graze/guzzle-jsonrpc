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

use Graze\GuzzleHttp\JsonRpc\Test\UnitTestCase;

class ErrorSubscriberTest extends UnitTestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Event emitter no longer exists on the request');
        $this->subscriber = new ErrorSubscriber();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('GuzzleHttp\Event\SubscriberInterface', $this->subscriber);
    }

    public function testGetEvents()
    {
        $this->assertEquals([
            'complete' => ['onRpcError']
        ], $this->subscriber->getEvents());
    }

    public function testOnRpcError()
    {
        $event = $this->mockCompleteEvent();
        $request = $this->mockRequest();
        $response = $this->mockResponse();

        $event->shouldReceive('getResponse')->once()->withNoArgs()->andReturn($response);
        $event->shouldReceive('getRequest')->once()->withNoArgs()->andReturn($request);
        $request->shouldReceive('getUrl')->once()->withNoArgs()->andReturn('http://foo');
        $request->shouldReceive('getRpcMethod')->once()->withNoArgs()->andReturn('foo');
        $response->shouldReceive('getRpcErrorCode')->times(2)->withNoArgs()->andReturn(1);
        $response->shouldReceive('getRpcErrorMessage')->once()->withNoArgs()->andReturn('bar');
        $response->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);

        $this->setExpectedException('Graze\GuzzleHttp\JsonRpc\Exception\RequestException');
        $this->subscriber->onRpcError($event);
    }

    public function testOnRpcErrorWithSuccessfulResponse()
    {
        $event = $this->mockCompleteEvent();
        $response = $this->mockResponse();

        $event->shouldReceive('getResponse')->once()->withNoArgs()->andReturn($response);
        $response->shouldReceive('getRpcErrorCode')->once()->withNoArgs();

        $this->subscriber->onRpcError($event);
    }
}
