<?php
namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Guzzle\Tests\GuzzleTestCase as TestCase;
use Mockery as m;

class ResponseTest extends TestCase
{
    public function setUp()
    {
        $this->httpResponse = m::mock('Guzzle\\Http\\Message\\Response');
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
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn(array());

        $responseData = array(
            'jsonrpc' => '2.0',
            'result' => array('foo', 'bar'),
            'id' => 1
        );

        $response = new Response($this->httpResponse, $responseData);

        $this->assertSame($responseData['jsonrpc'], $response->getVersion());
        $this->assertSame($responseData['result'], $response->getResult());
        $this->assertSame($responseData['id'], $response->getId());
    }

    public function testWithNoId()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn(array());

        $this->setExpectedException('OutOfRangeException');

        $response = new Response($this->httpResponse, array(
            'jsonrpc' => '2.0',
            'result' => array('foo', 'bar')
        ));
    }

    public function testWithNoResult()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn(array());

        $this->setExpectedException('OutOfRangeException');

        $response = new Response($this->httpResponse, array(
            'jsonrpc' => '2.0',
            'id' => 1
        ));
    }

    public function testWithNoVersion()
    {
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs()->andReturn(array());

        $this->setExpectedException('OutOfRangeException');

        $response = new Response($this->httpResponse, array(
            'result' => array('foo', 'bar'),
            'id' => 1
        ));
    }
}
