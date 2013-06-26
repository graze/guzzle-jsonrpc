<?php
namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Mockery as m;

class ErrorResponseTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function setUp()
    {
        $this->decorated = m::mock('Guzzle\\Http\\Message\\Response');
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
        $this->decorated->shouldReceive('getStatusCode')
             ->once()
             ->withNoArgs()
             ->andReturn(200);
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());

        $response = new ErrorResponse($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'error'   => array(
                'code'    => ErrorResponse::INVALID_REQUEST,
                'message' => 'foo'
            ),
            'id'      => 1
        ));

        $this->assertSame($data['jsonrpc'], $response->getVersion());
        $this->assertSame($data['id'],      $response->getId());

        $this->assertSame($data['error']['code'], $response->getCode());
        $this->assertNull($response->getData());
        $this->assertSame($data['error']['message'], $response->getMessage());
    }

    public function testWithValidErrorData()
    {
        $this->decorated->shouldReceive('getStatusCode')
             ->once()
             ->withNoArgs()
             ->andReturn(200);
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());

        $response = new ErrorResponse($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'error'   => array(
                'code'    => ErrorResponse::INVALID_REQUEST,
                'message' => 'foo',
                'data'    => array('bar' => 'baz')
            ),
            'id'      => 1
        ));

        $this->assertSame($data['jsonrpc'], $response->getVersion());
        $this->assertSame($data['id'],      $response->getId());

        $this->assertSame($data['error']['code'], $response->getCode());
        $this->assertSame($data['error']['data'], $response->getData());
        $this->assertSame($data['error']['message'], $response->getMessage());
    }

    public function testWithNoError()
    {
        $this->decorated->shouldReceive('getStatusCode')
             ->once()
             ->withNoArgs()
             ->andReturn(200);
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'id'      => 1
        ));
    }

    public function testWithNoErrorCode()
    {
        $this->decorated->shouldReceive('getStatusCode')
             ->once()
             ->withNoArgs()
             ->andReturn(200);
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'error'   => array(
                'message' => 'foo'
            ),
            'id'      => 1
        ));
    }

    public function testWithInvalidCode()
    {
        $this->decorated->shouldReceive('getStatusCode')
             ->once()
             ->withNoArgs()
             ->andReturn(200);
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());

        $this->setExpectedException('OutOfBoundsException');

        $response = new ErrorResponse($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'error'   => array(
                'code'    => 1,
                'message' => 'foo'
            ),
            'id'      => 1
        ));
    }

    public function testWithNoErrorMessage()
    {
        $this->decorated->shouldReceive('getStatusCode')
             ->once()
             ->withNoArgs()
             ->andReturn(200);
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'error'   => array(
                'code'    => ErrorResponse::INVALID_REQUEST
            ),
            'id'      => 1
        ));
    }

    public function testWithNoId()
    {
        $this->decorated->shouldReceive('getStatusCode')
             ->once()
             ->withNoArgs()
             ->andReturn(200);
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'error'   => array(
                'code'    => ErrorResponse::INVALID_REQUEST,
                'message' => 'foo'
            )
        ));
    }

    public function testWithNoVersion()
    {
        $this->decorated->shouldReceive('getStatusCode')
             ->once()
             ->withNoArgs()
             ->andReturn(200);
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());

        $this->setExpectedException('OutOfRangeException');

        $response = new ErrorResponse($this->decorated, $data = array(
            'error'   => array(
                'code'    => ErrorResponse::INVALID_REQUEST,
                'message' => 'foo'
            ),
            'id'      => 1
        ));
    }
}
