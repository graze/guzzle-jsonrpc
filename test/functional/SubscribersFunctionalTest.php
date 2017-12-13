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

use Graze\GuzzleHttp\JsonRpc\Subscriber\ErrorSubscriber;
use Graze\GuzzleHttp\JsonRpc\Test\FunctionalTestCase;

class SubscribersFunctionalTest extends FunctionalTestCase
{
    /**
     * @expectedException \Graze\GuzzleHttp\JsonRpc\Exception\ClientException
     */
    public function testSubscribersGetAddedToTheHttpClient()
    {
        $subscriber = new ErrorSubscriber();
        $client = $this->createClient(
            null,
            [
                'subscribers' => [$subscriber],
            ]
        );

        $id = 'abc';
        $method = 'bar';
        $request = $client->request($id, $method, []);

        $this->assertEquals(ClientInterface::SPEC, $request->getRpcVersion());
        $this->assertEquals($id, $request->getRpcId());
        $this->assertEquals($method, $request->getRpcMethod());
        $this->assertEquals(null, $request->getRpcParams());

        $client->send($request);
    }
}
