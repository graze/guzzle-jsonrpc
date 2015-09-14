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
namespace Graze\GuzzleHttp\JsonRpc\Exception;

use Graze\GuzzleHttp\JsonRpc\Test\UnitTestCase;

class RequestExceptionTest extends UnitTestCase
{
    public function setUp()
    {
        $this->request = $this->mockRequest();
        $this->response = $this->mockResponse();
    }

    public function dataCreateClientException()
    {
        return [[-32600], [-32601], [-32602], [-32700]];
    }

    public function dataCreateServerException()
    {
        return [[-32603], [-32000], [-32099], [-10000]];
    }

    /**
     * @dataProvider dataCreateClientException
     */
    public function testCreateClientException($code)
    {
        $this->request->shouldReceive('getRequestTarget')->once()->withNoArgs()->andReturn('http://foo');
        $this->request->shouldReceive('getRpcMethod')->once()->withNoArgs()->andReturn('foo');
        $this->response->shouldReceive('getRpcErrorCode')->once()->withNoArgs()->andReturn($code);
        $this->response->shouldReceive('getRpcErrorMessage')->once()->withNoArgs()->andReturn('bar');
        $this->response->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);

        $exception = RequestException::create($this->request, $this->response);
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Exception\ClientException', $exception);
    }

    /**
     * @dataProvider dataCreateServerException
     */
    public function testCreateServerException($code)
    {
        $this->request->shouldReceive('getRequestTarget')->once()->withNoArgs()->andReturn('http://foo');
        $this->request->shouldReceive('getRpcMethod')->once()->withNoArgs()->andReturn('foo');
        $this->response->shouldReceive('getRpcErrorCode')->once()->withNoArgs()->andReturn($code);
        $this->response->shouldReceive('getRpcErrorMessage')->once()->withNoArgs()->andReturn('bar');
        $this->response->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);

        $exception = RequestException::create($this->request, $this->response);
        $this->assertInstanceOf('Graze\GuzzleHttp\JsonRpc\Exception\ServerException', $exception);
    }
}
