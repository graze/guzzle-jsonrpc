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

namespace Graze\GuzzleHttp\JsonRpc;

use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;

interface ClientInterface
{
    /**
     * @const string
     */
    const SPEC = '2.0';

    /**
     * Build a notification request object.
     *
     * A Notification is a Request object without an `id` parameter. A Request
     * object that is a Notification signifies the Client's lack of interest in
     * the corresponding Response, and as such no Response object will be
     * returned by the request.
     *
     * @link   http://www.jsonrpc.org/specification#notification
     *
     * @param  string           $method
     * @param  array            $params
     *
     * @return RequestInterface
     */
    public function notification($method, array $params = null);

    /**
     * Build a request object.
     *
     * A RPC call is represented by sending a Request object to a server with a
     * corresponding `id` parameter. A valid Response object will yield the same
     * value for `id`.
     *
     * @link   http://www.jsonrpc.org/specification#request_object
     *
     * @param  mixed            $id
     * @param  string           $method
     * @param  array            $params
     *
     * @return RequestInterface
     */
    public function request($id, $method, array $params = null);

    /**
     * Send a request.
     *
     * This method sends a single request to the RPC server. The type of
     * response is determined by the type of request.
     *
     * @param  RequestInterface       $request
     *
     * @return ResponseInterface|null
     */
    public function send(RequestInterface $request);

    /**
     * Send a request asynchronously.
     *
     * This method sends a single request to the RPC server. The type of
     * response is determined by the type of request.
     *
     * @param  RequestInterface       $request
     *
     * @return PromiseInterface
     */
    public function sendAsync(RequestInterface $request);

    /**
     * Send a batch of requests.
     *
     * The intention of this method is to send the requests as a Batch Request
     * where possible, and as separate requests where not possible. One reason
     * a batch request isn't possible is where request URLs don't match.
     *
     * @link   http://www.jsonrpc.org/specification#batch
     *
     * @param  RequestInterface[]  $requests
     *
     * @return ResponseInterface[]
     */
    public function sendAll(array $requests);

    /**
     * Send an asynchronous batch of requests.
     *
     * The intention of this method is to send the requests as a Batch Request
     * where possible, and as separate requests where not possible. One reason
     * a batch request isn't possible is where request URLs don't match.
     *
     * @link   http://www.jsonrpc.org/specification#batch
     *
     * @param  RequestInterface[]  $requests
     *
     * @return PromiseInterface
     */
    public function sendAllAsync(array $requests);
}
