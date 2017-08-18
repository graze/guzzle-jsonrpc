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

namespace Graze\Guzzle\JsonRpc\Message;

use Graze\Guzzle\JsonRpc\JsonRpcClientInterface;
use Guzzle\Common\Collection;
use RuntimeException;

class Request extends AbstractRequest implements RequestInterface
{
    /**
     * @var Collection
     */
    protected $rpcFields;

    /**
     * @param string $url
     * @param array|Collection $headers
     */
    public function __construct($url, $headers = [])
    {
        parent::__construct($url, $headers);

        $this->rpcFields = new Collection();
        $this->setRpcVersion(JsonRpcClientInterface::VERSION);
    }

    /**
     * @return Response|null
     */
    public function send()
    {
        $this->setBody($this->jsonEncode($this->getRpcData()), self::CONTENT_TYPE);
        $id = $this->getRpcId();
        $response = $this->sendEntityEnclosingRequest();

        if (null !== $id) {
            $data = $response->json();
            if (!isset($data['id']) || $id !== $data['id']) {
                throw new RuntimeException('Response with ID "' . $id . '" expected.');
            }

            if (!array_key_exists('result', $data)) {
                return new ErrorResponse($response, $data);
            }

            return new Response($response, $data);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getRpcData()
    {
        return $this->rpcFields->getAll();
    }

    /**
     * {@inheritdoc}
     *
     * @return integer|string
     */
    public function getRpcId()
    {
        return $this->rpcFields->get('id');
    }

    /**
     * {@inheritdoc}
     *
     * @param integer|string $id
     */
    public function setRpcId($id)
    {
        $this->rpcFields->set('id', $id);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getRpcMethod()
    {
        return $this->rpcFields->get('method');
    }

    /**
     * {@inheritdoc}
     *
     * @param string $method
     */
    public function setRpcMethod($method)
    {
        $this->rpcFields->set('method', (string) $method);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getRpcParams()
    {
        return $this->rpcFields->get('params');
    }

    /**
     * {@inheritdoc}
     *
     * @param array $params
     */
    public function setRpcParams(array $params)
    {
        $this->rpcFields->set('params', $params);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getRpcVersion()
    {
        return $this->rpcFields->get('jsonrpc');
    }

    /**
     * {@inheritdoc}
     *
     * @param string $version
     */
    public function setRpcVersion($version)
    {
        $this->rpcFields->set('jsonrpc', (string) $version);
    }
}
