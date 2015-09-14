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

use Graze\GuzzleHttp\JsonRpc\Exception\RequestException;
use Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class RpcErrorMiddleware extends AbstractMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function applyResponse(HttpRequestInterface $request, HttpResponseInterface $response, array $options)
    {
        if ($response instanceof ResponseInterface &&
            isset($options['rpc_error']) &&
            true === $options['rpc_error'] &&
            null !== $response->getRpcErrorCode()
        ) {
            throw RequestException::create($request, $response);
        }

        return $response;
    }
}
