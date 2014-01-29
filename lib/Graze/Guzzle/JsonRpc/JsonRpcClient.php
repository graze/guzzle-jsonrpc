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
use Guzzle\Service\Client;
use Guzzle\Http\Message\RequestInterface;

class JsonRpcClient extends Client implements JsonRpcClientInterface
{
    /**
     * @param JsonRpcRequest[] $requests
     * @param string $uri
     * @param array $headers
     * @return BatchRequest
     */
    public function batch(array $requests, $uri = null, $headers = null)
    {
        return new BatchRequest($this->createRequest(RequestInterface::POST, $uri, $headers), $requests);
    }

    /**
     * @param string $method
     * @param array $params
     * @param string $uri
     * @param array $headers
     */
    public function notification($method, $params = null, $uri = null, $headers = null)
    {
        $request = new Request($this->createRequest(RequestInterface::POST, $uri, $headers), $method);
        $request->setRpcField('params', $params);

        return $request;
    }

    /**
     * @param string $method
     * @param integer $id
     * @param string $uri
     * @param array $headers
     * @return JsonRpcRequest
     */
    public function request($method, $id, $params = null, $uri = null, $headers = null)
    {
        $request = new Request($this->createRequest(RequestInterface::POST, $uri, $headers), $method, $id);
        $request->setRpcField('params', $params);

        return $request;
    }
}
