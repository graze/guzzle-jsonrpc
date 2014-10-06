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
namespace Graze\GuzzleHttp\JsonRpc\Subscriber;

use Graze\GuzzleHttp\JsonRpc\Exception\RequestException;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\SubscriberInterface;

class ErrorSubscriber implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            'complete' => ['onRpcError']
        ];
    }

    /**
     * @param CompleteEvent $event
     */
    public function onRpcError(CompleteEvent $event)
    {
        $response = $event->getResponse();

        if ($response && null !== $response->getRpcErrorCode()) {
            throw RequestException::create($event->getRequest(), $response);
        }
    }
}
