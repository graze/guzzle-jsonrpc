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

use Graze\GuzzleHttp\JsonRpc;
use Graze\GuzzleHttp\JsonRpc\Message\MessageFactory;
use Graze\GuzzleHttp\JsonRpc\Message\MessageFactoryInterface;
use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface;
use Graze\GuzzleHttp\JsonRpc\Middleware\RequestFactoryMiddleware;
use Graze\GuzzleHttp\JsonRpc\Middleware\RequestHeaderMiddleware;
use Graze\GuzzleHttp\JsonRpc\Middleware\ResponseFactoryMiddleware;
use Graze\GuzzleHttp\JsonRpc\Middleware\RpcErrorMiddleware;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;

class Client implements ClientInterface
{
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var MessageFactoryInterface
     */
    protected $messageFactory;

    /**
     * @param HttpClientInterface     $httpClient
     * @param MessageFactoryInterface $factory
     */
    public function __construct(HttpClientInterface $httpClient, MessageFactoryInterface $factory)
    {
        $this->httpClient = $httpClient;
        $this->messageFactory = $factory;

        $handler = $this->httpClient->getConfig('handler');
        $handler->push(new RequestFactoryMiddleware($factory));
        $handler->push(new RequestHeaderMiddleware());
        $handler->push(new RpcErrorMiddleware());
        $handler->push(new ResponseFactoryMiddleware($factory));
    }

    /**
     * @param  string $uri
     * @param  array  $config
     *
     * @return Client
     */
    public static function factory($uri, array $config = [])
    {
        if (isset($config['message_factory'])) {
            $factory = $config['message_factory'];
            unset($config['message_factory']);
        } else {
            $factory = new MessageFactory();
        }

        return new self(new HttpClient(array_merge($config, [
            'base_uri' => $uri,
        ])), $factory);
    }

    /**
     * {@inheritdoc}
     */
    public function notification($method, array $params = null)
    {
        return $this->createRequest(RequestInterface::NOTIFICATION, array_filter([
            'jsonrpc' => self::SPEC,
            'method' => $method,
            'params' => $params,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function request($id, $method, array $params = null)
    {
        return $this->createRequest(RequestInterface::REQUEST, array_filter([
            'jsonrpc' => self::SPEC,
            'method' => $method,
            'params' => $params,
            'id' => $id,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request)
    {
        $promise = $this->sendAsync($request);

        return $promise->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsync(RequestInterface $request)
    {
        return $this->httpClient->sendAsync($request)->then(
            function (ResponseInterface $response) use ($request) {
                return $request->getRpcId() ? $response : null;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sendAll(array $requests)
    {
        $promise = $this->sendAllAsync($requests);

        return $promise->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function sendAllAsync(array $requests)
    {
        return $this->httpClient->sendAsync($this->createRequest(
            RequestInterface::BATCH,
            $this->getBatchRequestOptions($requests)
        ))->then(function (ResponseInterface $response) {
            return $this->getBatchResponses($response);
        });
    }

    /**
     * @param  string           $method
     * @param  array            $options
     *
     * @return RequestInterface
     */
    protected function createRequest($method, $options)
    {
        $uri = $this->httpClient->getConfig('base_uri');
        $defaults = $this->httpClient->getConfig('defaults');
        $headers = isset($defaults['headers']) ? $defaults['headers'] : [];

        return $this->messageFactory->createRequest($method, $uri, $headers, $options);
    }

    /**
     * @param  RequestInterface[] $requests
     *
     * @return array
     */
    protected function getBatchRequestOptions(array $requests)
    {
        return array_map(function (RequestInterface $request) {
            return JsonRpc\json_decode((string) $request->getBody());
        }, $requests);
    }

    /**
     * @param  ResponseInterface $response
     *
     * @return ResponseInterface[]
     */
    protected function getBatchResponses(ResponseInterface $response)
    {
        $results = JsonRpc\json_decode((string) $response->getBody(), true);

        return array_map(function (array $result) use ($response) {
            return $this->messageFactory->createResponse(
                $response->getStatusCode(),
                $response->getHeaders(),
                $result
            );
        }, $results);
    }
}
