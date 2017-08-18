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
use OutOfRangeException;

class Response extends BaseResponse implements ResponseInterface
{
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

        $this->rpcFields = new Collection($data);

        foreach (['jsonrpc', 'id', 'result'] as $key) {
            if (!$this->rpcFields->hasKey($key)) {
                throw new OutOfRangeException('Parameter "' . $key . '" expected but not provided.');
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function getId()
    {
        return $this->rpcFields->get('id');
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->rpcFields->get('result');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->rpcFields->get('jsonrpc');
    }
}
