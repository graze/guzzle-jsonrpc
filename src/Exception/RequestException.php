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

use Exception;
use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException as HttpRequestException;

class RequestException extends HttpRequestException
{
    /**
     * {@inheritdoc}
     */
    public static function create(
        RequestInterface $request,
        ResponseInterface $response,
        Exception $previous = null
    ) {
        static $clientErrorCodes = [-32600, -32601, -32602, -32700];

        $errorCode = $response->getRpcErrorCode();
        if (in_array($errorCode, $clientErrorCodes)) {
            $label = 'Client RPC error response';
            $className = __NAMESPACE__ . '\\ClientException';
        } else {
            $label = 'Server RPC error response';
            $className = __NAMESPACE__ . '\\ServerException';
        }

        $message = $label . ' [url] ' . $request->getUrl()
            . ' [method] ' . $request->getRpcMethod()
            . ' [error code] ' . $errorCode
            . ' [error message] ' . $response->getRpcErrorMessage();

        return new $className($message, $request, $response, $previous);
    }
}
