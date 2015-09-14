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

class RequestFactoryMiddlewareTest extends UnitTestCase
{
    public function setUp()
    {
        $this->request = $this->mockRequest();
        $this->response = $this->mockResponse();
        $this->factory = $this->mockMessageFactory();

        $this->middleware = new RequestFactoryMiddleware($this->factory);
    }

    public function testApplyRequest()
    {
        $newRequest = clone $this->request;
        $this->factory->shouldReceive('fromRequest')->once()->with($this->request)->andReturn($newRequest);

        $this->assertSame($newRequest, $this->middleware->applyRequest($this->request, []));
    }

    public function testApplyResponse()
    {
        $this->assertSame($this->response, $this->middleware->applyResponse($this->request, $this->response, []));
    }
}
