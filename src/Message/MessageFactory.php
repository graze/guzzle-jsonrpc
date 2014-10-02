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

use Graze\GuzzleHttp\JsonRpc as JsonRpc;
use GuzzleHttp\Message\MessageFactory as HttpMessageFactory;
use GuzzleHttp\Stream\Stream;

class MessageFactory extends HttpMessageFactory
{
    /**
     * {@inheritdoc}
     */
    public function createRequest($method, $url, array $options = [])
    {
        $config  = isset($options['config']) ? $options['config'] : [];
        $jsonrpc = isset($options['jsonrpc']) ? $options['jsonrpc'] : [];
        $jsonrpc = $this->addIdToRequest($method, $jsonrpc);

        unset($options['config'], $options['jsonrpc']);

        $request = new Request('POST', $url, [], null, $config);
        $request->setHeader('Content-Type', 'application/json');
        $request->setBody(Stream::factory(JsonRpc\json_encode($jsonrpc)));

        $this->applyOptions($request, $options);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse($statusCode, array $headers = [], $body = null, array $options = [])
    {
        if (null !== $body) {
            $body = Stream::factory($body);
        }

        return new Response($statusCode, $headers, $body, $options);
    }

    /**
     * @param  string $method
     * @param  array  $data
     * @return array
     */
    protected function addIdToRequest($method, array $data)
    {
        if (RequestInterface::REQUEST === $method && !isset($data['id'])) {
            $data['id'] = uniqid(true);
        }

        return $data;
    }
}
