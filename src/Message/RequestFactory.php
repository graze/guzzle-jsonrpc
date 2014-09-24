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

use Guzzle\Http\Message\RequestFactory as BaseRequestFactory;
use RuntimeException;

class RequestFactory extends BaseRequestFactory
{
    /**
     * @var string
     **/
    protected $rpcBatchRequestClass = 'Graze\\Guzzle\\JsonRpc\\Message\\BatchRequest';
    protected $rpcRequestClass = 'Graze\\Guzzle\\JsonRpc\\Message\\Request';

    /**
     * {@inheritdoc}
     */
    public function create($method, $url, $headers = null, $body = null, array $options = array())
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
                throw new RuntimeExteption('Unsupported method type "' . $method . '".');
        }

        $request = new $class($url, $headers);

        if ($options) {
            $this->applyOptions($request, $options);
        }

        return $request;
    }
}
