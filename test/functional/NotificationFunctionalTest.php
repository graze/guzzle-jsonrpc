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

class NotificationFunctionalTest extends FunctionalTestCase
{
    public function setUp()
    {
        $this->client = $this->createClient();
    }

    public function testNotifyRequest()
    {
        $method = 'notify';
        $params = ['foo'=>true];
        $request = $this->client->notification($method, $params);
        $response = $this->client->send($request);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals(null, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals($params, $request->getRpcParams());

        $this->assertNull($response);
    }

    public function testNotifyRequestWithInvalidParams()
    {
        $method = 'notify';
        $params = ['foo'=>'bar'];
        $request = $this->client->notification($method, $params);
        $response = $this->client->send($request);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals(null, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals($params, $request->getRpcParams());

        $this->assertNull($response);
    }
}
