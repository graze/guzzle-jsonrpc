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

use Closure;
use Graze\GuzzleHttp\JsonRpc\Message\MessageFactory;
use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface;
use Graze\GuzzleHttp\JsonRpc\Subscriber\RequestSubscriber;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Message\MessageFactoryInterface;
use GuzzleHttp\Utils as GuzzleUtils;

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
        return new self(new HttpClient(array_replace_recursive([
            'base_url' => $url,
            'message_factory' => self::createMessageFactory(),
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
    public function request($id, $method, array $params = null)
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
        $response = $this->httpClient->send($this->createRequest(
            RequestInterface::BATCH,
            $this->getBatchRequestOptions($requests)
        ));

        return $this->getBatchResponses($response);
    }

    /**
     * @return MessageFactoryInterface
     */
    protected static function createMessageFactory()
    {
        return new MessageFactory();
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

    /**
     * @return MessageFactoryInterface
     */
    protected function getMessageFactory()
    {
        // This is pretty lame, but we need the factory from the client
        $factoryExtractor = Closure::bind(function () {
            return $this->messageFactory;
        }, $this->httpClient, $this->httpClient);

        return $factoryExtractor();
    }

    /**
     * @param  RequestInterface[] $requests
     * @return array
     */
    protected function getBatchRequestOptions(array $requests)
    {
        return array_map(function (RequestInterface $request) {
            return GuzzleUtils::jsonDecode((string) $request->getBody());
        }, $requests);
    }

    /**
     * @param  ResponseInterface  $response
     * @return RequestInterface[]
     */
    protected function getBatchResponses(ResponseInterface $response)
    {
        $factory = $this->getMessageFactory();
        $results = GuzzleUtils::jsonDecode((string) $response->getBody(), true);

        return array_map(function (array $result) use ($factory, $response) {
            return $factory->createResponse(
                $response->getStatusCode(),
                $response->getHeaders(),
                Utils::jsonEncode($result)
            );
        }, $results);
    }
}
