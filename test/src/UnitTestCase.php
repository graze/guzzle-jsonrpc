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
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class UnitTestCase extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @return Mockery\MockInterface
     */
    protected function mockBeforeEvent()
    {
        return Mockery::mock('GuzzleHttp\Event\BeforeEvent');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockCompleteEvent()
    {
        return Mockery::mock('GuzzleHttp\Event\CompleteEvent');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockErrorEvent()
    {
        return Mockery::mock('GuzzleHttp\Event\ErrorEvent');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockHttpClient()
    {
        return Mockery::mock('GuzzleHttp\ClientInterface');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockHttpHandler()
    {
        return Mockery::mock('GuzzleHttp\HandlerStack');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockMessageFactory()
    {
        return Mockery::mock('Graze\GuzzleHttp\JsonRpc\Message\MessageFactoryInterface');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockPromise()
    {
        return Mockery::mock('GuzzleHttp\Promise\PromiseInterface');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockRequest()
    {
        return Mockery::mock('Graze\GuzzleHttp\JsonRpc\Message\RequestInterface');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockResponse()
    {
        return Mockery::mock('Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockStream()
    {
        return Mockery::mock('GuzzleHttp\Stream\StreamInterface');
    }

    /**
     * @return Mockery\MockInterface
     */
    protected function mockTransaction()
    {
        return Mockery::mock('GuzzleHttp\Adapter\TransactionInterface');
    }
}
