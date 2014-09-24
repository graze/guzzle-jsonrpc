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
use Guzzle\Http\Message\EntityEnclosingRequest;
use OutOfBoundsException;
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
    public function __construct($url, $headers = array())
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
     */
    public function getRpcData()
    {
        return $this->rpcFields->getAll();
    }

    /**
     * {@inheritdoc}
     */
    public function getRpcId()
    {
        return $this->rpcFields->get('id');
    }

    /**
     * {@inheritdoc}
     */
    public function setRpcId($id)
    {
        $this->rpcFields->set('id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getRpcMethod()
    {
        return $this->rpcFields->get('method');
    }

    /**
     * {@inheritdoc}
     */
    public function setRpcMethod($method)
    {
        $this->rpcFields->set('method', (string) $method);
    }

    /**
     * {@inheritdoc}
     */
    public function getRpcParams()
    {
        return $this->rpcFields->get('params');
    }

    /**
     * {@inheritdoc}
     */
    public function setRpcParams(array $params)
    {
        $this->rpcFields->set('params', $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getRpcVersion()
    {
        return $this->rpcFields->get('jsonrpc');
    }

    /**
     * {@inheritdoc}
     */
    public function setRpcVersion($version)
    {
        $this->rpcFields->set('jsonrpc', (string) $version);
    }
}
