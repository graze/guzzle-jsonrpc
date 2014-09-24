<?php
/*
 * This file is part of Guzzle JSON-RPC
 *
 * Copyright (c) 2014 Nature Delivered Ltd. <http://graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see  http://github.com/graze/guzzle-jsonrpc/blob/master/LICENSE
 * @link http://github.com/graze/guzzle-jsonrpc
 */
namespace Graze\Guzzle\JsonRpc;

use Graze\Guzzle\JsonRpc\Message\BatchRequest;
use Graze\Guzzle\JsonRpc\Message\Request;
use Graze\Guzzle\JsonRpc\Message\RequestInterface;
use Guzzle\Http\ClientInterface;

interface JsonRpcClientInterface extends ClientInterface
{
    /**
     * @const string
     */
    const VERSION = '2.0';

    /**
     * Build a batch request object
     *
     * RPC calls can be requested in a batch rather than transported separately.
     * The requests can be a mix of both Request and Notification Request
     * objects. The Response will contain all responses given, with no entries
     * for Notifications.
     *
     * @param RequestInterface[] $requests
     * @param string $uri
     * @param array $headers
     * @return BatchRequest
     */
    public function batch(array $requests, $uri = null, array $headers = array());

    /**
     * Build a notification request object
     *
     * A Notification is a Request object without an `id` parameter. A Request
     * object that is a Notification signifies the Client's lack of interest in
     * the corresponding Response, and as such no Response object will be
     * returned by the request.
     *
     * @param string $method
     * @param array $params
     * @param string $uri
     * @param array $headers
     * @return Request
     */
    public function notification($method, array $params = array(), $uri = null, array $headers = array());

    /**
     * Build a request object
     *
     * A RPC call is represented by sending a Request object to a server with a
     * corresponding `id` parameter. A valid Response object will yield the same
     * value for `id`.
     *
     * @param string $method
     * @param mixed $id
     * @param array $params
     * @param string $uri
     * @param array $headers
     * @return Request
     */
    public function request($method, $id, array $params = array(), $uri = null, array $headers = array());
}
