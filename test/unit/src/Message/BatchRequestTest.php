<?php
namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Mockery as m;

class BatchRequestTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function setUp()
    {
        $this->client = m::mock('Graze\\Guzzle\\JsonRpc\\JsonRpcClientInterface');
        $this->decorated = m::mock('Guzzle\\Http\\Message\\RequestInterface');
    }

    public function testParent()
    {
        $this->assertInstanceOf('Guzzle\\Http\\Message\\EntityEnclosingRequest', m::mock('Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest'));
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

        $request = new BatchRequest($this->decorated, array(
            m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request'),
            m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request'),
            m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request')
        ));

        $this->assertSame($this->client, $request->getClient());
        $this->assertSame('BAR', $request->getMethod());
        $this->assertSame('', $request->getUrl());
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

        $request = new BatchRequest($this->decorated, array(
            m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request'),
            m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request'),
            m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request')
        ));

        $this->assertSame($this->client, $request->getClient());
        $this->assertSame('BAR', $request->getMethod());
        $this->assertSame('http://graze.com/foo', $request->getUrl());
    }

    public function testSend()
    {
        $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo'
        );

        $response = m::mock('Guzzle\\Http\\Message\\Response');
        $response->shouldReceive('json')
            ->once()
            ->withNoArgs()
            ->andReturn(array());

        $child = m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request');
        $child->shouldReceive('getRpcField')
              ->times(2)
              ->with('id');
        $child->shouldReceive('getRpcFields')
              ->once()
              ->withNoArgs()
              ->andReturn($data);

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');

        $request = new BatchRequest($this->decorated, array($child));
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $this->assertSame(array(), $request->send());

        $json = json_encode(array($data), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->assertSame($json, (string) $request->getBody());
    }

    public function testSendWithIdAndSuccessfulResponse()
    {
        $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo'
        );

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
            ->andReturn(array(array('jsonrpc' => '2.0', 'id' => 1, 'result' => array('foo'))));

        $child = m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request');
        $child->shouldReceive('getRpcField')
              ->times(4)
              ->with('id')
              ->andReturn(1);
        $child->shouldReceive('getRpcFields')
              ->once()
              ->withNoArgs()
              ->andReturn($data);

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');

        $request = new BatchRequest($this->decorated, array($child));
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $responseArray = $request->send();
        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\Response', reset($responseArray));

        $json = json_encode(array($data), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->assertSame($json, (string) $request->getBody());
    }

    public function testSendWithIdAndErrorResponse()
    {
        $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo'
        );

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
            ->andReturn(array(array(
                'jsonrpc' => '2.0',
                'id' => 1,
                'error' => array('code' => ErrorResponse::INVALID_REQUEST, 'message' => 'foo')
            )));

        $child = m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request');
        $child->shouldReceive('getRpcField')
              ->times(4)
              ->with('id')
              ->andReturn(1);
        $child->shouldReceive('getRpcFields')
              ->once()
              ->withNoArgs()
              ->andReturn($data);

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');

        $request = new BatchRequest($this->decorated, array($child));
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $responseArray = $request->send();
        $this->assertInstanceOf('Graze\\Guzzle\\JsonRpc\\Message\\ErrorResponse', reset($responseArray));

        $json = json_encode(array($data), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->assertSame($json, (string) $request->getBody());
    }

    public function testSendWithDuplicateId()
    {
        $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo'
        );

        $response = m::mock('Guzzle\\Http\\Message\\Response');

        $childA = m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request');
        $childA->shouldReceive('getRpcField')
               ->times(3)
               ->with('id')
               ->andReturn(1);
        $childA->shouldReceive('getRpcFields')
               ->once()
               ->withNoArgs()
               ->andReturn($data);

        $childB = m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request');
        $childB->shouldReceive('getRpcField')
               ->times(3)
               ->with('id')
               ->andReturn(1);

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');

        $request = new BatchRequest($this->decorated, array($childA, $childB));

        $this->setExpectedException('LogicException');
        $request->send();
    }

    public function testSendWithIdThrowsIfIdNotReturned()
    {
        $data = array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => 'foo'
        );

        $response = m::mock('Guzzle\\Http\\Message\\Response');
        $response->shouldReceive('json')
            ->once()
            ->withNoArgs()
            ->andReturn(array(array('jsonrpc' => '2.0', 'result' => array('foo'))));

        $child = m::mock('Graze\\Guzzle\\JsonRpc\\Message\\Request');
        $child->shouldReceive('getRpcField')
              ->times(4)
              ->with('id')
              ->andReturn(1);
        $child->shouldReceive('getRpcFields')
              ->once()
              ->withNoArgs()
              ->andReturn($data);

        $this->decorated->shouldReceive('getClient')->andReturn($this->client);
        $this->decorated->shouldReceive('getHeaders');
        $this->decorated->shouldReceive('getMethod');
        $this->decorated->shouldReceive('getUrl');

        $request = new BatchRequest($this->decorated, array($child));
        $this->client->shouldReceive('send')
             ->once()
             ->with($request)
             ->andReturn($response);

        $this->setExpectedException('RuntimeException');
        $request->send();
    }
}
