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

use Graze\GuzzleHttp\JsonRpc\Adapter\BatchAdapter;
use Graze\GuzzleHttp\JsonRpc\Message\MessageFactory;
use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Subscriber\RequestSubscriber;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Adapter\ParallelAdapterInterface;
use GuzzleHttp\Message\MessageFactoryInterface;

class Client implements ClientInterface
{
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param  string $url
     * @param  array  $config
     * @return Client
     */
    public static function factory($url, array $config = [])
    {
        $messageFactory = self::createMessageFactory();
        $parallelAdapter = self::createParallelAdapter($messageFactory);

        return new self(new HttpClient(array_replace_recursive([
            'base_url' => $url,
            'message_factory' => $messageFactory,
            'parallel_adapter' => $parallelAdapter,
            'defaults' => [
                'headers' => [
                    'Accept-Encoding' => 'gzip;q=1.0,deflate;q=0.6,identity;q=0.3'
                ]
            ]
        ], $config)));
    }

    /**
     * {@inheritdoc}
     */
    public function notification($method, array $params = null)
    {
        return $this->createRequest(RequestInterface::NOTIFICATION, array_filter([
            'jsonrpc' => self::SPEC,
            'method'  => $method,
            'params'  => $params
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function request($method, array $params = null, $id = null)
    {
        return $this->createRequest(RequestInterface::REQUEST, array_filter([
            'jsonrpc' => self::SPEC,
            'method'  => $method,
            'params'  => $params,
            'id'      => $id
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function send(RequestInterface $request)
    {
        $response = $this->httpClient->send($request);

        return $request->getRpcId() ? $response : null;
    }

    /**
     * {@inheritdoc}
     */
    public function sendAll(array $requests)
    {
        $responses = new RequestSubscriber();
        $listeners = array_map(function ($method) use ($responses) {
            return [$responses, reset($method)];
        }, $responses->getEvents());

        $this->httpClient->sendAll($requests, $listeners);

        return $responses->getAll();
    }

    /**
     * @return MessageFactoryInterface
     */
    protected static function createMessageFactory()
    {
        return new MessageFactory();
    }

    /**
     * @param  MessageFactoryInterface  $factory
     * @return ParallelAdapterInterface
     */
    protected static function createParallelAdapter(MessageFactoryInterface $factory)
    {
        return new BatchAdapter($factory);
    }

    /**
     * @param  string           $method
     * @param  array            $options
     * @return RequestInterface
     */
    protected function createRequest($method, $options)
    {
        return $this->httpClient->createRequest($method, null, [
            'jsonrpc' => $options
        ]);
    }
}
