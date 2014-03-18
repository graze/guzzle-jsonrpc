<?php
namespace Graze\Guzzle\JsonRpc;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Mockery as m;

class JsonRpcClientTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function setup()
    {
        $this->base = 'http://graze.com';
        $this->client = new JsonRpcClient($this->base);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\JsonRpcClientInterface', $this->client);
    }

    public function testParent()
    {
        $this->assertInstanceOf('Guzzle\\Service\\Client', $this->client);
    }

    public function testBatch()
    {
        $request = $this->client->batch(array());

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base, $request->getUrl());
    }

    public function testBatchWithUri()
    {
        $request = $this->client->batch(array(), 'foo');

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base . '/foo', $request->getUrl());
    }

    public function testBatchWithSubRequests()
    {
        $request = $this->client->batch(array(
            m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request')
        ));

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base, $request->getUrl());
    }

    public function testNotification()
    {
        $request = $this->client->notification('foo');

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base, $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertSame(array(), $request->getRpcField('params'));
        $this->assertNull($request->getRpcField('id'));
    }

    public function testNotificationWithParams()
    {
        $request = $this->client->notification('foo', array('bar'));

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base, $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertSame(array('bar'), $request->getRpcField('params'));
        $this->assertNull($request->getRpcField('id'));
    }

    public function testNotificationWithUri()
    {
        $request = $this->client->notification('foo', array(), 'bar');

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base . '/bar', $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertSame(array(), $request->getRpcField('params'));
        $this->assertNull($request->getRpcField('id'));
    }

    public function testRequest()
    {
        $request = $this->client->request('foo', 1);

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base, $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertSame(array(), $request->getRpcField('params'));
        $this->assertSame(1, $request->getRpcField('id'));
    }

    public function testRequestWithParams()
    {
        $request = $this->client->request('foo', 1, array('bar'));

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base, $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertSame(array('bar'), $request->getRpcField('params'));
        $this->assertSame(1, $request->getRpcField('id'));
    }

    public function testRequestWithUri()
    {
        $request = $this->client->request('foo', 1, array(), 'bar');

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Request', $request);
        $this->assertSame(RequestInterface::POST, $request->getMethod());
        $this->assertSame($this->base . '/bar', $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertSame(array(), $request->getRpcField('params'));
        $this->assertSame(1, $request->getRpcField('id'));
    }
}
