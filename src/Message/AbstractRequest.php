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

use Guzzle\Http\Message\EntityEnclosingRequest;

abstract class AbstractRequest extends EntityEnclosingRequest
{
    /**
     * @param string $url
     * @param array|Collection $headers
     */
    public function __construct($url, $headers = [])
    {
        parent::__construct(self::POST, $url, $headers);
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    /**
     * @return Response
     */
    protected function sendEntityEnclosingRequest()
    {
        return parent::send();
    }
}
