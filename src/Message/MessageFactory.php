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

use Graze\GuzzleHttp\JsonRpc;
use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

class MessageFactory implements MessageFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest($method, $uri, array $headers = [], array $options = [])
    {
        $body = JsonRpc\json_encode($this->addIdToRequest($method, $options));

        return new Request('POST', $uri, $headers, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse($statusCode, array $headers = [], array $options = [])
    {
        $body = JsonRpc\json_encode($options);

        return new Response($statusCode, $headers, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function fromRequest(HttpRequestInterface $request)
    {
        return $this->createRequest(
            $request->getMethod(),
            $request->getUri(),
            $request->getHeaders(),
            JsonRpc\json_decode((string) $request->getBody(), true) ?: []
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fromResponse(HttpResponseInterface $response)
    {
        return $this->createResponse(
            $response->getStatusCode(),
            $response->getHeaders(),
            JsonRpc\json_decode((string) $response->getBody(), true) ?: []
        );
    }

    /**
     * @param  string $method
     * @param  array  $data
     *
     * @return array
     */
    protected function addIdToRequest($method, array $data)
    {
        if (RequestInterface::REQUEST === $method && ! isset($data['id'])) {
            $data['id'] = uniqid(true);
        }

        return $data;
    }
}
