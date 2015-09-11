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

class RequestHeaderMiddleware
{
    /**
     * @param  HttpRequestInterface $response
     * @return HttpRequestInterface
     */
    public function __invoke(HttpRequestInterface $request)
    {
        return $request
            ->withHeader('Accept-Encoding', 'gzip;q=1.0,deflate;q=0.6,identity;q=0.3')
            ->withHeader('Content-Type', 'application/json');
    }
}
