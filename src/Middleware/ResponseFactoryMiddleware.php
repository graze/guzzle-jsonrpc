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
namespace Graze\GuzzleHttp\JsonRpc\Middleware;

use Graze\GuzzleHttp\JsonRpc\Message\MessageFactoryInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class ResponseFactoryMiddleware
{
    /**
     * @var MessageFactoryInterface
     */
    protected $factory;

    /**
     * @param MessageFactoryInterface $factory
     */
    public function __construct(MessageFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param  HttpResponseInterface $response
     * @return HttpResponseInterface
     */
    public function __invoke(HttpResponseInterface $response)
    {
        return $this->factory->fromResponse($response);
    }
}
