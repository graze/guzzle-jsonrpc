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

namespace Graze\GuzzleHttp\JsonRpc\Message;

use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface MessageFactoryInterface
{
    /**
     * @param strint            $uri
     * @param string            $method
     * @param array             $headers
     * @param array             $options
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri, array $headers = [], array $options = []);

    /**
     * @param int                $statusCode
     * @param array              $headers
     * @param array              $options
     *
     * @return ResponseInterface
     */
    public function createResponse($statusCode, array $headers = [], array $options = []);

    /**
     * @param  HttpRequestInterface $request
     *
     * @return RequestInterface
     */
    public function fromRequest(HttpRequestInterface $request);

    /**
     * @param  HttpRequestInterface $request
     *
     * @return RequestInterface
     */
    public function fromResponse(HttpResponseInterface $response);
}
