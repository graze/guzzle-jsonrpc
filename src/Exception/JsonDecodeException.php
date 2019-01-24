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

namespace Graze\GuzzleHttp\JsonRpc\Exception;

use Throwable;

class JsonDecodeException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    private $json;

    /**
     * JsonDecodeException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param $json
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null, $json)
    {
        parent::__construct($message, $code, $previous);
        $this->json = $json;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return $this->json;
    }
}
