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
namespace Graze\GuzzleHttp\JsonRpc;

if (!defined('GRAZE_GUZZLEHTTP_JSONRPC_FUNCTIONS')) {

    define('GRAZE_GUZZLEHTTP_JSONRPC_FUNCTIONS', ClientInterface::SPEC);

    /**
     * Wrapper for json_encode that includes character escaping by default
     *
     * @param  mixed          $data
     * @param  boolean        $escapeChars
     * @return string|boolean
     */
    function json_encode($data, $escapeChars = true)
    {
        $options =
            \JSON_HEX_AMP  |
            \JSON_HEX_APOS |
            \JSON_HEX_QUOT |
            \JSON_HEX_TAG  |
            \JSON_UNESCAPED_UNICODE |
            \JSON_UNESCAPED_SLASHES;

        return \json_encode($data, $escapeChars ? $options : 0);
    }
}
