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
namespace Graze\GuzzleHttp\JsonRpc\Middleware;

use Graze\GuzzleHttp\JsonRpc\Test\UnitTestCase;

class ResponseFactoryMiddlewareTest extends UnitTestCase
{
    public function setUp()
    {
        $this->response = $this->mockResponse();
        $this->factory = $this->mockMessageFactory();
    }

    public function testInvoke()
    {
        $middleware = new ResponseFactoryMiddleware($this->factory);
        $newResponse = clone $this->response;

        $this->factory->shouldReceive('fromResponse')->once()->with($this->response)->andReturn($newResponse);

        $this->assertSame($newResponse, $middleware($this->response));
    }
}
