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

use Guzzle\Common\Collection;
use Guzzle\Http\Message\Response as BaseResponse;
use OutOfBoundsException;
use OutOfRangeException;

class ErrorResponse extends BaseResponse implements ResponseInterface
{
    /**
     * @const integer
     */
    const INVALID_REQUEST  = -32600;
    const INVALID_PARAMS   = -32602;
    const INTERNAL_ERROR   = -32603;
    const METHOD_NOT_FOUND = -32601;
    const PARSE_ERROR      = -32700;

    /**
     * @var Collection
     */
    protected $rpcError;

    /**
     * @var Collection
     */
    protected $rpcFields;

    /**
     * @param BaseResponse $response
     * @param array $data
     */
    public function __construct(BaseResponse $response, array $data)
    {
        parent::__construct($response->getStatusCode(), $response->getHeaders());

        $this->rpcError  = new Collection(isset($data['error']) ? $data['error'] : array());
        $this->rpcFields = new Collection($data);

        foreach (array('jsonrpc', 'id', 'error') as $key) {
            if (!$this->rpcFields->hasKey($key)) {
                throw new OutOfRangeException('Parameter "' . $key . '" expected but not provided.');
            }
        }

        foreach (array('code', 'message') as $key) {
            if (!$this->rpcError->hasKey($key)) {
                throw new OutOfRangeException('Parameter "' . $key . '" expected in error but not provided.');
            }
        }

        if (!$this->isCodeValid($this->getCode())) {
            throw new OutOfBoundsException('The error code "' . $this->getCode() . '" is not valid.');
        }
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->rpcError->get('code');
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->rpcError->get('data');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->rpcFields->get('id');
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->rpcError->get('message');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->rpcFields->get('jsonrpc');
    }

    /**
     * @param integer $code
     * @return boolean
     */
    protected function isCodeValid($code)
    {
        return is_int($code);
    }
}
