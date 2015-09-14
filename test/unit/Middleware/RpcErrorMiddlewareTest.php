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

class RpcMiddlewareMiddlewareTest extends UnitTestCase
{
    public function setUp()
    {
        $this->request = $this->mockRequest();
        $this->response = $this->mockResponse();

        $this->middleware = new RpcErrorMiddleware();
    }

    public function testApplyRequest()
    {
        $this->assertSame($this->request, $this->middleware->applyRequest($this->request, []));
    }

    public function testApplyResponseThrowsClientException()
    {
        $this->response->shouldReceive('getRpcErrorCode')->times(2)->withNoArgs()->andReturn(-32600);
        $this->request->shouldReceive('getRequestTarget')->once()->withNoArgs()->andReturn('http://foo');
        $this->request->shouldReceive('getRpcMethod')->once()->withNoArgs()->andReturn('foo');
        $this->response->shouldReceive('getRpcErrorMessage')->once()->withNoArgs()->andReturn('bar');
        $this->response->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);

        $this->setExpectedException('Graze\GuzzleHttp\JsonRpc\Exception\ClientException');
        $this->middleware->applyResponse($this->request, $this->response, ['rpc_error' => true]);
    }

    public function testApplyResponseThrowsServerException()
    {
        $this->response->shouldReceive('getRpcErrorCode')->times(2)->withNoArgs()->andReturn(-32000);
        $this->request->shouldReceive('getRequestTarget')->once()->withNoArgs()->andReturn('http://foo');
        $this->request->shouldReceive('getRpcMethod')->once()->withNoArgs()->andReturn('foo');
        $this->response->shouldReceive('getRpcErrorMessage')->once()->withNoArgs()->andReturn('bar');
        $this->response->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);

        $this->setExpectedException('Graze\GuzzleHttp\JsonRpc\Exception\ServerException');
        $this->middleware->applyResponse($this->request, $this->response, ['rpc_error' => true]);
    }

    public function testApplyResponseNoError()
    {
        $this->response->shouldReceive('getRpcErrorCode')->once()->withNoArgs()->andReturn(null);

        $this->assertSame($this->response, $this->middleware->applyResponse($this->request, $this->response, ['rpc_error' => true]));
    }

    public function testApplyResponseNoOption()
    {
        $this->assertSame($this->response, $this->middleware->applyResponse($this->request, $this->response, []));
    }
}
