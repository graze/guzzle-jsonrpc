<?php
namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Mockery as m;

class RequestTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function setUp()
    {
        $this->client = m::mock('Graze\\Guzzle\\JsonRpc\\JsonRpcClientInterface');
        $this->decorated = m::mock('Guzzle\\Http\\Message\\RequestInterface');
        $this->dispatcher = m::mock('Symfony\\Component\\EventDispatcher\\EventDispatcherInterface');
    }

    public function testParent()
    {
        $this->assertInstanceOf('Guzzle\\Http\\Message\\EntityEnclosingRequest', m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request'));
    }

    public function testInstance()
    {
        $this->decorated->shouldReceive('getMethod')
             ->once()
             ->withNoArgs()
             ->andReturn('BAR');
        $this->decorated->shouldReceive('getUrl')
             ->once()
             ->withNoArgs();
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());
        $this->decorated->shouldReceive('getClient')
             ->once()
             ->withNoArgs()
             ->andReturn($this->client);
        $this->client->shouldReceive('getEventDispatcher')
             ->once()
             ->withNoArgs()
             ->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener')
             ->once()
             ->with('request.error', m::type('array'), -255);

        $request = new Request($this->decorated, 'foo');

        $this->assertSame($this->client, $request->getClient());
        $this->assertSame('BAR', $request->getMethod());
        $this->assertSame('', $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertNull($request->getRpcField('params'));
        $this->assertNull($request->getRpcField('id'));
    }

    public function testInstanceWithId()
    {
        $this->decorated->shouldReceive('getMethod')
             ->once()
             ->withNoArgs()
             ->andReturn('BAR');
        $this->decorated->shouldReceive('getUrl')
             ->once()
             ->withNoArgs();
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());
        $this->decorated->shouldReceive('getClient')
             ->once()
             ->withNoArgs()
             ->andReturn($this->client);
        $this->client->shouldReceive('getEventDispatcher')
             ->once()
             ->withNoArgs()
             ->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener')
             ->once()
             ->with('request.error', m::type('array'), -255);

        $request = new Request($this->decorated, 'foo', 1);

        $this->assertSame($this->client, $request->getClient());
        $this->assertSame('BAR', $request->getMethod());
        $this->assertSame('', $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertNull($request->getRpcField('params'));
        $this->assertSame(1, $request->getRpcField('id'));
    }

    public function testInstanceWithUrl()
    {
        $this->decorated->shouldReceive('getMethod')
             ->once()
             ->withNoArgs()
             ->andReturn('BAR');
        $this->decorated->shouldReceive('getUrl')
             ->once()
             ->withNoArgs()
             ->andReturn('http://graze.com/foo');
        $this->decorated->shouldReceive('getHeaders')
             ->once()
             ->withNoArgs()
             ->andReturn(array());
        $this->decorated->shouldReceive('getClient')
             ->once()
             ->withNoArgs()
             ->andReturn($this->client);
        $this->client->shouldReceive('getEventDispatcher')
             ->once()
             ->withNoArgs()
             ->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener')
             ->once()
             ->with('request.error', m::type('array'), -255);

        $request = new Request($this->decorated, 'foo');

        $this->assertSame($this->client, $request->getClient());
        $this->assertSame('BAR', $request->getMethod());
        $this->assertSame('http://graze.com/foo', $request->getUrl());
        $this->assertSame(JsonRpcClientInterface::VERSION, $request->getRpcField('jsonrpc'));
        $this->assertSame('foo', $request->getRpcField('method'));
        $this->assertNull($request->getRpcField('params'));
        $this->assertNull($request->getRpcField('id'));
    }

    public function testSend()
    {
        $response = m::mock('Guzzle\\Http\\Message\\Response');

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo');
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $this->assertNull($request->send());

        $json = json_encode($request->getRpcFields(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->assertSame($json, (string) $request->getBody());
        $this->assertSame(array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo'
        ), $request->getRpcFields());
    }

    public function testSendWithIdAndSuccessfulResponse()
    {
        $response = m::mock('Guzzle\\Http\\Message\\Response');
        $response->shouldReceive('getStatusCode')
            ->once()
            ->withNoArgs()
            ->andReturn(200);
        $response->shouldReceive('getHeaders')
            ->once()
            ->withNoArgs()
            ->andReturn(array());
        $response->shouldReceive('json')
            ->once()
            ->withNoArgs()
            ->andReturn(array('jsonrpc' => '2.0', 'id' => 1, 'result' => array('foo')));

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo', 1);
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Response', $request->send());

        $json = json_encode($request->getRpcFields(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->assertSame($json, (string) $request->getBody());
        $this->assertSame(array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo',
            'id'      => 1
        ), $request->getRpcFields());
    }

    public function testSendWithIdAndErrorResponse()
    {
        $response = m::mock('Guzzle\\Http\\Message\\Response');
        $response->shouldReceive('getStatusCode')
            ->once()
            ->withNoArgs()
            ->andReturn(200);
        $response->shouldReceive('getHeaders')
            ->once()
            ->withNoArgs()
            ->andReturn(array());
        $response->shouldReceive('json')
            ->once()
            ->withNoArgs()
            ->andReturn(array(
                'jsonrpc' => '2.0',
                'id' => 1,
                'error' => array('code' => ErrorResponse::INVALID_REQUEST, 'message' => 'foo')
            ));

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo', 1);
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\ErrorResponse', $request->send());

        $json = json_encode($request->getRpcFields(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->assertSame($json, (string) $request->getBody());
        $this->assertSame(array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo',
            'id'      => 1
        ), $request->getRpcFields());
    }

    public function testSendWithIdThrowsIfResponseDoesNotMatch()
    {
        $response = m::mock('Guzzle\\Http\\Message\\Response');
        $response->shouldReceive('json')
            ->once()
            ->withNoArgs()
            ->andReturn(array(
                'jsonrpc' => '2.0',
                'id' => 2,
                'error' => array('code' => ErrorResponse::INVALID_REQUEST, 'message' => 'foo')
            ));

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo', 1);
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $this->setExpectedException('RuntimeException');
        $request->send();
    }

    public function testSendWithParams()
    {
        $response = m::mock('Guzzle\\Http\\Message\\Response');

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo');
        $request->setRpcField('params', array('bar' => 'baz'));
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $this->assertNull($request->send());

        $json = json_encode($request->getRpcFields(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->assertSame($json, (string) $request->getBody());
        $this->assertSame(array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo',
            'params'  => array('bar' => 'baz')
        ), $request->getRpcFields());
    }

    public function testGetRpcField()
    {
        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo');
        $this->assertSame('foo', $request->getRpcField('method'));
    }

    public function testGetRpcFieldWithInvalidName()
    {
        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo');
        $this->assertNull($request->getRpcField('foo'));
    }

    public function testGetRpcFields()
    {
        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo');
        $this->assertSame(array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo'
        ), $request->getRpcFields());
    }

    public function testSetRpcFieldAddsNew()
    {
        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo');
        $request->setRpcField('foo', 'bar');
        $this->assertSame(array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo',
            'foo'     => 'bar'
        ), $request->getRpcFields());
    }

    public function testSetRpcFieldOverwrites()
    {
        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');
        $this->client->shouldReceive('getEventDispatcher')->andReturn($this->dispatcher);
        $this->dispatcher->shouldReceive('addListener');

        $request = new Request($this->decorated, 'foo');
        $request->setRpcField('method', 'bar');
        $this->assertSame(array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'bar'
        ), $request->getRpcFields());
    }
}
