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

class RequestSubscriberTest extends UnitTestCase
{
    public function setUp()
    {
        $this->subscriber = new RequestSubscriber();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('GuzzleHttp\Event\SubscriberInterface', $this->subscriber);
    }

    public function testGetEvents()
    {
        $this->assertEquals([
            'before'   => ['clear'],
            'complete' => ['onComplete']
        ], $this->subscriber->getEvents());
    }

    public function testClear()
    {
        $this->subscriber->clear();

        $this->assertEquals([], $this->subscriber->getAll());
    }

    public function testOnComplete()
    {
        $event = $this->mockCompleteEvent();
        $response = $this->mockResponse();

        $event->shouldReceive('getResponse')->once()->withNoArgs()->andReturn($response);
        $response->shouldReceive('getRpcId')->once()->withNoArgs()->andReturn(1);

        $this->subscriber->onComplete($event);

        $this->assertEquals([$response], $this->subscriber->getAll());
    }

    public function testOnCompleteWithNotification()
    {
        $event = $this->mockCompleteEvent();
        $response = $this->mockResponse();

        $event->shouldReceive('getResponse')->once()->withNoArgs()->andReturn($response);
        $response->shouldReceive('getRpcId')->once()->withNoArgs()->andReturn(null);

        $this->subscriber->onComplete($event);

        $this->assertEquals([], $this->subscriber->getAll());
    }
}
