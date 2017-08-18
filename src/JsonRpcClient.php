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
use Graze\Guzzle\JsonRpc\Message\RequestFactory;
use Graze\Guzzle\JsonRpc\Message\RequestInterface;
use Guzzle\Common\Collection;
use Guzzle\Service\Client;
use RuntimeException;

class JsonRpcClient extends Client implements JsonRpcClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string           $baseUrl Base URL of the web service
     * @param array|Collection $config  Configuration settings
     *
     * @throws RuntimeException if cURL is not installed
     */
    public function __construct($baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);

        $this->setRequestFactory($this->getDefaultRequestFactory());
        $this->setDefaultHeaders([
            'Accept-Encoding' => 'gzip;q=1.0,deflate;q=0.6,identity;q=0.3'
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param RequestInterface[] $requests
     * @param string $uri
     * @param array $headers
     * @return BatchRequest
     */
    public function batch(array $requests, $uri = null, array $headers = [])
    {
        $request = $this->createRequest(RequestInterface::BATCH, $uri, $headers);
        $request->setRequests($requests);

        return $request;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $method
     * @param array $params
     * @param string $uri
     * @param array $headers
     * @return Request
     */
    public function notification($method, array $params = [], $uri = null, array $headers = [])
    {
        $request = $this->createRequest(RequestInterface::NOTIFICATION, $uri, $headers);
        $request->setRpcMethod($method);
        $request->setRpcParams($params);

        return $request;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $method
     * @param mixed $id
     * @param array $params
     * @param string $uri
     * @param array $headers
     * @return Request
     */
    public function request($method, $id, array $params = [], $uri = null, array $headers = [])
    {
        $request = $this->createRequest(RequestInterface::REQUEST, $uri, $headers);
        $request->setRpcMethod($method);
        $request->setRpcParams($params);
        $request->setRpcId($id);

        return $request;
    }

    /**
     * @return RequestFactory
     */
    protected function getDefaultRequestFactory()
    {
        return new RequestFactory();
    }
}
