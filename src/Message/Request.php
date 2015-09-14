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
use GuzzleHttp\Psr7\Request as HttpRequest;

class Request extends HttpRequest implements RequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRpcId()
    {
        return $this->getFieldFromBody('id');
    }

    /**
     * {@inheritdoc}
     */
    public function getRpcMethod()
    {
        return $this->getFieldFromBody('method');
    }

    /**
     * {@inheritdoc}
     */
    public function getRpcParams()
    {
        return $this->getFieldFromBody('params');
    }

    /**
     * {@inheritdoc}
     */
    public function getRpcVersion()
    {
        return $this->getFieldFromBody('jsonrpc');
    }

    /**
     * @param  string $key
     *
     * @return mixed
     */
    protected function getFieldFromBody($key)
    {
        $rpc = JsonRpc\json_decode((string) $this->getBody(), true);

        return isset($rpc[$key]) ? $rpc[$key] : null;
    }
}
