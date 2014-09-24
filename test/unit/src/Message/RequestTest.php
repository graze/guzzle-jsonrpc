<?php
namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Guzzle\Tests\GuzzleTestCase as TestCase;
use Mockery as m;

class RequestTest extends TestCase
{
    public function setUp()
    {
        $this->url = 'http://graze.com/foo';
        $this->client = m::mock('Guzzle\Http\ClientInterface');
        $this->httpResponse = m::mock('Guzzle\\Http\\Message\\Response');

        $this->request = new Request($this->url);
        $this->request->setClient($this->client);
    }

    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    public function testParent()
    {
        $this->assertInstanceOf('Guzzle\\Http\\Message\\EntityEnclosingRequest', $this->request);
    }

    public function testGetUrl()
    {
        $this->assertEquals($this->url, $this->request->getUrl());
    }

    public function testGetRpcVersion()
    {
        $this->assertEquals('2.0', $this->request->getRpcVersion());
    }

    public function testSend()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo');
        $this->client->shouldReceive('send')->once()->with($this->request)->andReturn($this->httpResponse);

        $this->request->setRpcMethod('foo');
        $response = $this->request->send();

        $this->assertNull($response);
        $this->assertSame($this->jsonEncode($requestData), (string) $this->request->getBody());
    }

    public function testSendWithId()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array('jsonrpc'=>'2.0', 'result'=>array('foo'), 'id'=>1);
        $this->client->shouldReceive('send')->once()->with($this->request)->andReturn($this->httpResponse);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs();

        $this->request->setRpcMethod('foo');
        $this->request->setRpcId(1);
        $response = $this->request->send();

        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\Message\Response', $response);
        $this->assertSame($this->jsonEncode($requestData), (string) $this->request->getBody());
    }

    public function testSendWithIdAndNullResult()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array('jsonrpc'=>'2.0', 'result'=>null, 'id'=>1);
        $this->client->shouldReceive('send')->once()->with($this->request)->andReturn($this->httpResponse);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs();

        $this->request->setRpcMethod('foo');
        $this->request->setRpcId(1);
        $response = $this->request->send();

        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\Message\Response', $response);
        $this->assertSame($this->jsonEncode($requestData), (string) $this->request->getBody());
    }

    public function testSendWithIdAndErrorResult()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array('jsonrpc'=>'2.0', 'id'=>1, 'error'=>array('message'=>'', 'code'=>0));
        $this->client->shouldReceive('send')->once()->with($this->request)->andReturn($this->httpResponse);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs();

        $this->request->setRpcMethod('foo');
        $this->request->setRpcId(1);
        $response = $this->request->send();

        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\Message\ErrorResponse', $response);
        $this->assertSame($this->jsonEncode($requestData), (string) $this->request->getBody());
    }

    public function testSendWithIdThrowsIfResponseDoesNotMatch()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array('jsonrpc'=>'2.0', 'result'=>array('foo'), 'id'=>2);
        $this->client->shouldReceive('send')->once()->with($this->request)->andReturn($this->httpResponse);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);

        $this->request->setRpcMethod('foo');
        $this->request->setRpcId(1);

        $this->setExpectedException('RuntimeException');
        $response = $this->request->send();
    }

    public function testSendWithParams()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1, 'params'=>array('foo'));
        $responseData = array('jsonrpc'=>'2.0', 'result'=>array('foo'), 'id'=>1);
        $this->client->shouldReceive('send')->once()->with($this->request)->andReturn($this->httpResponse);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs();

        $this->request->setRpcMethod('foo');
        $this->request->setRpcId(1);
        $this->request->setRpcParams(array('foo'));
        $response = $this->request->send();

        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\Message\Response', $response);
        $this->assertSame($this->jsonEncode($requestData), (string) $this->request->getBody());
    }
}
