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

use Psr\Http\Message\MessageInterface as HttpMessageInterface;

interface MessageInterface extends HttpMessageInterface
{
    /**
     * @return mixed
     */
    public function getRpcId();

    /**
     * @return string
     */
    public function getRpcVersion();
}
