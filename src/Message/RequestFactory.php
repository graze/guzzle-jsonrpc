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
use Guzzle\Http\EntityBodyInterface;
use Guzzle\Http\Message\RequestFactory as BaseRequestFactory;
use Guzzle\Http\Url;
use RuntimeException;

class RequestFactory extends BaseRequestFactory
{
    /** @var string */
    protected $rpcBatchRequestClass = 'Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest';
    /** @var string */
    protected $rpcRequestClass = 'Graze\\Guzzle\\JsonRpc\\Message\\Request';

    /**
     * {@inheritdoc}
     *
     * @param string                                    $method  HTTP method (GET, POST, PUT, PATCH, HEAD, DELETE, ...)
     * @param string|Url                                $url     HTTP URL to connect to
     * @param array|Collection                          $headers HTTP headers
     * @param string|resource|array|EntityBodyInterface $body    Body to send in the request
     * @param array                                     $options Array of options to apply to the request
     *
     * @return RequestInterface
     */
    public function create($method, $url, $headers = null, $body = null, array $options = [])
    {
        switch ($method) {
            case RequestInterface::BATCH:
                $class = $this->rpcBatchRequestClass;
                break;
            case RequestInterface::NOTIFICATION:
            case RequestInterface::REQUEST:
                $class = $this->rpcRequestClass;
                break;
            default:
                throw new RuntimeException('Unsupported method type "' . $method . '".');
        }

        $request = new $class($url, $headers);

        if ($options) {
            $this->applyOptions($request, $options);
        }

        return $request;
    }
}
