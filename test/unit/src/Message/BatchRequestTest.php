<?php
namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Guzzle\Tests\GuzzleTestCase as TestCase;
use Mockery as m;

class BatchRequestTest extends TestCase
{
    public function setUp()
    {
        $this->url = 'http://graze.com/foo';
        $this->client = m::mock('Guzzle\Http\ClientInterface');
        $this->request = m::mock('Graze\Guzzle\JsonRpc\Message\Request');
        $this->httpResponse = m::mock('Guzzle\\Http\\Message\\Response');

        $this->batch = new BatchRequest($this->url);
        $this->batch->setClient($this->client);
    }

    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    public function testParent()
    {
        $this->assertInstanceOf('Guzzle\Http\Message\EntityEnclosingRequest', new BatchRequest($this->url));
    }

    public function testGetUrl()
    {
        $this->assertEquals($this->url, $this->batch->getUrl());
    }

    public function testSend()
    {
        $responseData = array(array('jsonrpc'=>'2.0', 'result'=>array('foo')));
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);
        $this->client->shouldReceive('send')->once()->with($this->batch)->andReturn($this->httpResponse);

        $this->assertSame(array(), $this->batch->send());
    }

    public function testSendWithIdAndSuccessfulResponse()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array(array('jsonrpc'=>'2.0', 'id'=>1, 'result'=>array('foo')));

        $this->batch->addRequest($this->request);
        $this->client->shouldReceive('send')->once()->with($this->batch)->andReturn($this->httpResponse);
        $this->request->shouldReceive('getRpcId')->times(2)->withNoArgs()->andReturn(1);
        $this->request->shouldReceive('getRpcData')->once()->withNoArgs()->andReturn($requestData);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs();

        $result = $this->batch->send();

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Response', reset($result));
        $this->assertSame($this->jsonEncode(array($requestData)), (string) $this->batch->getBody());
    }

    public function testSendWithIdAndNullResult()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array(array('jsonrpc'=>'2.0', 'id'=>1, 'result'=>null));

        $this->batch->addRequest($this->request);
        $this->client->shouldReceive('send')->once()->with($this->batch)->andReturn($this->httpResponse);
        $this->request->shouldReceive('getRpcId')->times(2)->withNoArgs()->andReturn(1);
        $this->request->shouldReceive('getRpcData')->once()->withNoArgs()->andReturn($requestData);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs();

        $result = $this->batch->send();

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Response', reset($result));
        $this->assertSame($this->jsonEncode(array($requestData)), (string) $this->batch->getBody());
    }

    public function testSendWithIdReturnsError()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array(array('jsonrpc'=>'2.0', 'id'=>1, 'error'=>array('message'=>'', 'code'=>0)));

        $this->batch->addRequest($this->request);
        $this->client->shouldReceive('send')->once()->with($this->batch)->andReturn($this->httpResponse);
        $this->request->shouldReceive('getRpcId')->times(2)->withNoArgs()->andReturn(1);
        $this->request->shouldReceive('getRpcData')->once()->withNoArgs()->andReturn($requestData);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);
        $this->httpResponse->shouldReceive('getStatusCode')->once()->withNoArgs()->andReturn(200);
        $this->httpResponse->shouldReceive('getHeaders')->once()->withNoArgs();

        $result = $this->batch->send();

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\ErrorResponse', reset($result));
        $this->assertSame($this->jsonEncode(array($requestData)), (string) $this->batch->getBody());
    }

    public function testSendWithDuplicateId()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array(array('jsonrpc'=>'2.0', 'id'=>1, 'result'=>null));

        $requestB = m::mock('Graze\Guzzle\JsonRpc\Message\Request');

        $this->batch->addRequest($this->request);
        $this->batch->addRequest($requestB);
        $this->request->shouldReceive('getRpcId')->once()->withNoArgs()->andReturn(1);
        $this->request->shouldReceive('getRpcData')->once()->withNoArgs()->andReturn($requestData);
        $requestB->shouldReceive('getRpcId')->once()->withNoArgs()->andReturn(1);

        $this->setExpectedException('LogicException');
        $result = $this->batch->send();
    }

    public function testSendWithIdThrowsIfIdNotReturned()
    {
        $requestData = array('jsonrpc'=>'2.0', 'method'=>'foo', 'id'=>1);
        $responseData = array();

        $this->batch->addRequest($this->request);
        $this->client->shouldReceive('send')->once()->with($this->batch)->andReturn($this->httpResponse);
        $this->request->shouldReceive('getRpcId')->times(2)->withNoArgs()->andReturn(1);
        $this->request->shouldReceive('getRpcData')->once()->withNoArgs()->andReturn($requestData);
        $this->httpResponse->shouldReceive('json')->once()->withNoArgs()->andReturn($responseData);

        $this->setExpectedException('RuntimeException');
        $result = $this->batch->send();
    }
}
