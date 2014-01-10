<?php
/*
 * This file is part of Guzzle JsonRpc
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
use Guzzle\Http\Message\RequestInterface;
use RuntimeException;

class Request extends EntityEnclosingRequest
{
    /**
     * @var Collection
     */
    protected $rpcFields;

    /**
     * @param RequestInterface $request
     * @param string $method
     * @param integer $id
     */
    public function __construct(RequestInterface $request, $method, $id = null)
    {
        parent::__construct($request->getMethod(), $request->getUrl(), $request->getHeaders());

        $this->setClient($request->getClient());
        $this->rpcFields = new Collection(array(
            'jsonrpc' => JsonRpcClientInterface::VERSION,
            'method'  => (string) $method
        ));

        if (null !== $id) {
            $this->setRpcField('id', $id);
        }
    }

    /**
     * @return Response
     */
    public function send()
    {
        $this->setBody($this->jsonEncode($this->getRpcFields()));
        $response = parent::send();

        if ($this->rpcFields->hasKey('id')) {
            $data = $response->json();
            if (!isset($data['id']) || $this->getRpcField('id') !== $data['id']) {
                throw new RuntimeException('Response with ID "' . $this->getRpcField('id') . '" expected.');
            }

            return isset($data['result']) ? new Response($response, $data) : new ErrorResponse($response, $data);
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getRpcField($key)
    {
        return $this->rpcFields->get($key);
    }

    /**
     * @return array
     */
    public function getRpcFields()
    {
        return $this->rpcFields->getAll();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setRpcField($key, $value)
    {
        return $this->rpcFields->set($key, $value);
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }
}
