<?php

namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Guzzle\Tests\GuzzleTestCase as TestCase;
use Mockery as m;

class ErrorResponseTest extends TestCase
{
    public function setUp()
    {
        $this->httpResponse = m::mock('Guzzle\\Http\\Message\\Response');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\ResponseInterface', m::mock('Graze\\Guzzle\\JsonRpc\\Message\\ErrorResponse'));
    }

    public function testParent()
    {
        $this->assertInstanceOf('Guzzle\\Http\\Message\\Response', m::mock('Graze\\Guzzle\\JsonRpc\\Message\\ErrorResponse'));
    }

    public function testWithValidData()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        $responseData = [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => ErrorResponse::INVALID_REQUEST,
                'message' => 'foo'
            ],
            'id' => 1
        ];

        $response = new ErrorResponse($this->httpResponse, $responseData);

        $this->assertSame($responseData['jsonrpc'], $response->getVersion());
        $this->assertSame($responseData['id'], $response->getId());
        $this->assertSame($responseData['error']['code'], $response->getCode());
        $this->assertNull($response->getData());
        $this->assertSame($responseData['error']['message'], $response->getMessage());
    }

    public function testWithValidErrorData()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        $responseData = [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => ErrorResponse::INVALID_REQUEST,
                'message' => 'foo',
                'data' => ['bar' => 'baz']
            ],
            'id' => 1
        ];

        $response = new ErrorResponse($this->httpResponse, $responseData);

        $this->assertSame($responseData['jsonrpc'], $response->getVersion());
        $this->assertSame($responseData['id'], $response->getId());
        $this->assertSame($responseData['error']['code'], $response->getCode());
        $this->assertSame($responseData['error']['data'], $response->getData());
        $this->assertSame($responseData['error']['message'], $response->getMessage());
    }

    public function testWithNoError()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->httpResponse, [
            'jsonrpc' => '2.0',
            'id' => 1
        ]);
    }

    public function testWithNoErrorCode()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->httpResponse, [
            'jsonrpc' => '2.0',
            'error' => [
                'message' => 'foo'
            ],
            'id' => 1
        ]);
    }

    public function testWithInvalidCode()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        $this->setExpectedException('OutOfBoundsException');

        $response = new ErrorResponse($this->httpResponse, [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => 'foo',
                'message' => 'bar'
            ],
            'id' => 1
        ]);
    }

    public function testWithNoErrorMessage()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->httpResponse, [
            'jsonrpc' => '2.0',
            'error'   => [
                'code'    => ErrorResponse::INVALID_REQUEST
            ],
            'id'      => 1
        ]);
    }

    public function testWithNoId()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->httpResponse, [
            'jsonrpc' => '2.0',
            'error'   => [
                'code'    => ErrorResponse::INVALID_REQUEST,
                'message' => 'foo'
            ]
        ]);
    }

    public function testWithNoVersion()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn([]);

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->httpResponse, [
            'error'   => [
                'code'    => ErrorResponse::INVALID_REQUEST,
                'message' => 'foo'
            ],
            'id'      => 1
        ]);
    }
}
