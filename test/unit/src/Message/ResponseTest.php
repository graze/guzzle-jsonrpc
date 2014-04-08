<?php
namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Mockery as m;

class ResponseTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function setUp()
    {
        $this->decorated = m::mock('Guzzle\\Http\\Message\\Response');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\ResponseInterface', m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Response'));
    }

    public function testParent()
    {
        $this->assertInstanceOf('Guzzle\\Http\\Message\\Response', m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Response'));
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

        $response = new Response($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'result'  => array('foo', 'bar'),
            'id'      => 1
        ));

        $this->assertSame($data['jsonrpc'], $response->getVersion());
        $this->assertSame($data['result'],  $response->getResult());
        $this->assertSame($data['id'],      $response->getId());
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

        $response = new Response($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'result'  => array('foo', 'bar')
        ));
    }

    public function testWithNoResult()
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

        $response = new Response($this->decorated, $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'id'      => 1
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

        $response = new Response($this->decorated, $data = array(
            'result'  => array('foo', 'bar'),
            'id'      => 1
        ));
    }
}
