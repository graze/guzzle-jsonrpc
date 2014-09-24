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

use Guzzle\Http\Message\RequestInterface as BaseRequestInterface;

interface RequestInterface extends BaseRequestInterface
{
    const BATCH        = 'BATCH';
    const NOTIFICATION = 'NOTIFICATION';
    const REQUEST      = 'REQUEST';

    const CONTENT_TYPE = 'application/json';

    /**
     * @return array
     */
    public function getRpcData();

    /**
     * @return integer|string
     */
    public function getRpcId();

    /**
     * @param integer|string $id
     */
    public function setRpcId($id);

    /**
     * @return string
     */
    public function getRpcMethod();

    /**
     * @param string $method
     */
    public function setRpcMethod($method);

    /**
     * @return array
     */
    public function getRpcParams();

    /**
     * @param array $params
     */
    public function setRpcParams(array $params);

    /**
     * @return string
     */
    public function getRpcVersion();

    /**
     * @param string $version
     */
    public function setRpcVersion($version);
}
