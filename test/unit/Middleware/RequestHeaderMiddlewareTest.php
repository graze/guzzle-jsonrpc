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

class RequestHeaderMiddlewareTest extends UnitTestCase
{
    public function setUp()
    {
        $this->request = $this->mockRequest();
        $this->response = $this->mockResponse();

        $this->middleware = new RequestHeaderMiddleware();
    }

    public function testApplyRequest()
    {
        $requestA = clone $this->request;
        $requestB = clone $requestA;

        $this->request->shouldReceive('withHeader')->once()->with('Accept-Encoding', 'gzip;q=1.0,deflate;q=0.6,identity;q=0.3')->andReturn($requestA);
        $requestA->shouldReceive('withHeader')->once()->with('Content-Type', 'application/json')->andReturn($requestB);

        $this->assertSame($requestB, $this->middleware->applyRequest($this->request, []));
    }

    public function testApplyResponse()
    {
        $this->assertSame($this->response, $this->middleware->applyResponse($this->request, $this->response, []));
    }
}
