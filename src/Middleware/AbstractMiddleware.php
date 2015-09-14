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

use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

abstract class AbstractMiddleware
{
    public function __invoke(callable $fn)
    {
        return function (HttpRequestInterface $request, array $options) use ($fn) {
            return $fn(call_user_func([$this, 'applyRequest'], $request, $options), $options)->then(
                function (HttpResponseInterface $response) use ($request, $options) {
                    return call_user_func([$this, 'applyResponse'], $request, $response, $options);
                }
            );
        };
    }

    /**
     * @param  HttpRequestInterface $request
     * @param  array                $options
     *
     * @return HttpRequestInterface
     */
    public function applyRequest(HttpRequestInterface $request, array $options)
    {
        return $request;
    }

    /**
     * @param  HttpRequestInterface  $request
     * @param  HttpResponseInterface $response
     * @param  array                 $options
     *
     * @return HttpResponseInterface
     */
    public function applyResponse(HttpRequestInterface $request, HttpResponseInterface $response, array $options)
    {
        return $response;
    }
}
