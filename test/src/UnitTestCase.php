<?php

/*
 * This file is part of Guzzle HTTP JSON-RPC
 *
 * Copyright (c) 2014 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/guzzle-jsonrpc/blob/master/LICENSE
 * @link http://github.com/graze/guzzle-jsonrpc
 */

namespace Graze\GuzzleHttp\JsonRpc\Test;

use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

class UnitTestCase extends TestCase
{
    protected function mockBeforeEvent()
    {
        return Mockery::mock('GuzzleHttp\Event\BeforeEvent');
    }

    protected function mockCompleteEvent()
    {
        return Mockery::mock('GuzzleHttp\Event\CompleteEvent');
    }

    protected function mockErrorEvent()
    {
        return Mockery::mock('GuzzleHttp\Event\ErrorEvent');
    }

    protected function mockHttpClient()
    {
        return Mockery::mock('GuzzleHttp\ClientInterface');
    }

    protected function mockHttpHandler()
    {
        return Mockery::mock('GuzzleHttp\HandlerStack');
    }

    protected function mockMessageFactory()
    {
        return Mockery::mock('Graze\GuzzleHttp\JsonRpc\Message\MessageFactoryInterface');
    }

    protected function mockPromise()
    {
        return Mockery::mock('GuzzleHttp\Promise\PromiseInterface');
    }

    protected function mockRequest()
    {
        return Mockery::mock('Graze\GuzzleHttp\JsonRpc\Message\RequestInterface');
    }

    protected function mockResponse()
    {
        return Mockery::mock('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface');
    }

    protected function mockStream()
    {
        return Mockery::mock('GuzzleHttp\Stream\StreamInterface');
    }

    protected function mockTransaction()
    {
        return Mockery::mock('GuzzleHttp\Adapter\TransactionInterface');
    }
}
