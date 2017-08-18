<?php

namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Guzzle\Tests\GuzzleTestCase as TestCase;
use Mockery as m;

class RequestFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->factory = new RequestFactory();
    }

    public function testCreateBatch()
    {
        $request = $this->factory->create('BATCH', 'http://foo');

        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\Message\BatchRequest', $request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('http://foo', $request->getUrl());
    }

    public function testCreateNotification()
    {
        $request = $this->factory->create('NOTIFICATION', 'http://foo');

        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\Message\Request', $request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('http://foo', $request->getUrl());
    }

    public function testCreateRequest()
    {
        $request = $this->factory->create('REQUEST', 'http://foo');

        $this->assertInstanceOf('Graze\Guzzle\JsonRpc\Message\Request', $request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('http://foo', $request->getUrl());
    }
}
