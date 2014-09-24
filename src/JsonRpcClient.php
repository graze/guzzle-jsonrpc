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

use Graze\Guzzle\JsonRpc\Message\RequestFactory;
use Graze\Guzzle\JsonRpc\Message\RequestInterface;
use Guzzle\Service\Client;

class JsonRpcClient extends Client implements JsonRpcClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);

        $this->setRequestFactory($this->getDefaultRequestFactory());
        $this->setDefaultHeaders(array(
            'Accept-Encoding' => 'gzip;q=1.0,deflate;q=0.6,identity;q=0.3'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function batch(array $requests, $uri = null, array $headers = array())
    {
        $request = $this->createRequest(RequestInterface::BATCH, $uri, $headers);
        $request->setRequests($requests);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function notification($method, array $params = array(), $uri = null, array $headers = array())
    {
        $request = $this->createRequest(RequestInterface::NOTIFICATION, $uri, $headers);
        $request->setRpcMethod($method);
        $request->setRpcParams($params);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function request($method, $id, array $params = array(), $uri = null, array $headers = array())
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
