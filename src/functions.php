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

use InvalidArgumentException;

/**
 * Wrapper for JSON decode that implements error detection with helpful
 * error messages.
 *
 * @param string $json    JSON data to parse
 * @param bool $assoc     When true, returned objects will be converted
 *                        into associative arrays.
 * @param int    $depth   User specified recursion depth.
 * @param int    $options Bitmask of JSON decode options.
 *
 * @throws InvalidArgumentException if the JSON cannot be parsed.
 *
 * @return mixed
 *
 * @link http://www.php.net/manual/en/function.json-decode.php
 *
 * @copyright Copyright (c) 2011-2015 Michael Dowling, https://github.com/mtdowling <mtdowling@gmail.com>
 * @license MIT https://github.com/guzzle/guzzle/blob/5.3/LICENSE
 */
function json_decode($json, $assoc = false, $depth = 512, $options = 0)
{
    static $jsonErrors = [
         JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
         JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
         JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
         JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
         JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded',
    ];

    // Patched support for decoding empty strings for PHP 7+
    $data = \json_decode($json == "" ? "{}" : $json, $assoc, $depth, $options);

    if (JSON_ERROR_NONE !== json_last_error()) {
        $last = json_last_error();
        throw new InvalidArgumentException(
             'Unable to parse JSON data: '
             . (isset($jsonErrors[$last])
                 ? $jsonErrors[$last]
                 : 'Unknown error')
         );
    }

    return $data;
}

/**
 * Wrapper for json_encode that includes character escaping by default.
 *
 * @param  mixed          $data
 * @param  bool        $escapeChars
 *
 * @return string|bool
 */
function json_encode($data, $escapeChars = true)
{
    $options =
        JSON_HEX_AMP  |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_TAG  |
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES;

    return \json_encode($data, $escapeChars ? $options : 0);
}
