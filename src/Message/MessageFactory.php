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

use Graze\GuzzleHttp\JsonRpc\Utils;
use GuzzleHttp\Message\MessageFactory as HttpMessageFactory;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Url;

class MessageFactory extends HttpMessageFactory
{
    /**
     * {@inheritdoc}
     *
     * @param string     $method  HTTP method (GET, POST, PUT, etc.)
     * @param string|Url $url     HTTP URL to connect to
     * @param array      $options Array of options to apply to the request
     *
     * @return RequestInterface
     * @link http://docs.guzzlephp.org/en/latest/clients.html#request-options
     */
    public function createRequest($method, $url, array $options = [])
    {
        $config  = isset($options['config']) ? $options['config'] : [];
        $jsonrpc = isset($options['jsonrpc']) ? $options['jsonrpc'] : [];
        $jsonrpc = $this->addIdToRequest($method, $jsonrpc);

        unset($options['config'], $options['jsonrpc']);

        $request = new Request('POST', $url, [], null, $config);
        $request->setHeader('Content-Type', 'application/json');
        $request->setBody(Stream::factory(Utils::jsonEncode($jsonrpc)));

        $this->applyOptions($request, $options);

        return $request;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $statusCode HTTP status code
     * @param array  $headers    Response headers
     * @param mixed  $body       Response body
     * @param array  $options    Response options
     *     - protocol_version: HTTP protocol version
     *     - header_factory: Factory used to create headers
     *     - And any other options used by a concrete message implementation
     *
     * @return ResponseInterface
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
