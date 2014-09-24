<?php
namespace Graze\Guzzle\JsonRpc;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Guzzle\Tests\GuzzleTestCase as TestCase;
use Mockery as m;

class JsonRpcClientTest extends TestCase
{
    public function setup()
    {
        $this->base = 'http://graze.com';
        $this->client = new JsonRpcClient($this->base);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\JsonRpcClientInterface', $this->client);
    }

    public function testParent()
    {
        $this->assertInstanceOf('Guzzle\Service\Client', $this->client);
    }

    public function testBatch()
    {
        $request = $this->client->batch(array());

        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\Message\BatchRequest', $request);
        $this->assertEquals($this->base, $request->getUrl());
    }

    public function testBatchWithUri()
    {
        $request = $this->client->batch(array(), 'foo');

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest', $request);
        $this->assertEquals($this->base . '/foo', $request->getUrl());
    }

    public function testBatchWithHeaders()
    {
        $headers = array('foo' => 'bar');
        $request = $this->client->batch(array(), null, $headers);

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest', $request);
        $this->assertTrue($request->getHeaders()->get('foo')->hasValue('bar'));
    }

    public function testBatchWithSubRequests()
    {
        $request = $this->client->batch(array(
            m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request')
        ));

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest', $request);
    }

    public function testNotification()
    {
        $request = $this->client->notification('foo');

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertEquals($this->base, $request->getUrl());
        $this->assertEquals('2.0', $request->getRpcVersion());
        $this->assertEquals('foo', $request->getRpcMethod());
        $this->assertEquals(array(), $request->getRpcParams());
        $this->assertNull($request->getRpcId());
    }

    public function testNotificationWithParams()
    {
        $request = $this->client->notification('foo', array('bar'));

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertEquals($this->base, $request->getUrl());
        $this->assertEquals('2.0', $request->getRpcVersion());
        $this->assertEquals('foo', $request->getRpcMethod());
        $this->assertEquals(array('bar'), $request->getRpcParams());
        $this->assertNull($request->getRpcId());
    }

    public function testNotificationWithUri()
    {
        $request = $this->client->notification('foo', array(), 'bar');

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertEquals($this->base . '/bar', $request->getUrl());
        $this->assertEquals('2.0', $request->getRpcVersion());
        $this->assertEquals('foo', $request->getRpcMethod());
        $this->assertEquals(array(), $request->getRpcParams());
        $this->assertNull($request->getRpcId());
    }

    public function testRequest()
    {
        $request = $this->client->request('foo', 1);

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertEquals($this->base, $request->getUrl());
        $this->assertEquals('2.0', $request->getRpcVersion());
        $this->assertEquals('foo', $request->getRpcMethod());
        $this->assertEquals(array(), $request->getRpcParams());
        $this->assertEquals(1, $request->getRpcId());
    }

    public function testRequestWithParams()
    {
        $request = $this->client->request('foo', 1, array('bar'));

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertEquals($this->base, $request->getUrl());
        $this->assertEquals('2.0', $request->getRpcVersion());
        $this->assertEquals('foo', $request->getRpcMethod());
        $this->assertEquals(array('bar'), $request->getRpcParams());
        $this->assertEquals(1, $request->getRpcId());
    }

    public function testRequestWithUri()
    {
        $request = $this->client->request('foo', 1, array(), 'bar');

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertEquals($this->base . '/bar', $request->getUrl());
        $this->assertEquals('2.0', $request->getRpcVersion());
        $this->assertEquals('foo', $request->getRpcMethod());
        $this->assertEquals(array(), $request->getRpcParams());
        $this->assertEquals(1, $request->getRpcId());
    }
}
