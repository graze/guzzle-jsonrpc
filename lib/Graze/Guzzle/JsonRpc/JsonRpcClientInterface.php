<?php
/*
 * This file is part of Guzzle JsonRpc
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
use Guzzle\Http\ClientInterface;

interface JsonRpcClientInterface extends ClientInterface
{
    /**
     * @const string
     */
    const VERSION = '2.0';

    /**
     * @param Request[] $requests
     * @param string $uri
     * @param array $headers
     * @return BatchRequest
     */
    public function batch(array $requests, $uri = null, $headers = null);

    /**
     * @param string $method
     * @param array $params
     * @param string $uri
     * @param array $headers
     * @return Request
     */
    public function notification($method, $params = null, $uri = null, $headers = null);

    /**
     * @param string $method
     * @param integer $id
     * @param array $params
     * @param string $uri
     * @param array $headers
     * @return Request
     */
    public function request($method, $id, $params = null, $uri = null, $headers = null);
}
