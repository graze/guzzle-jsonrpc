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

use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\SubscriberInterface;
use SplObjectStorage;

class RequestSubscriber implements SubscriberInterface
{
    /**
     * @var SplObjectStorage
     */
    protected $responses;

    public function __construct()
    {
        $this->responses = new SplObjectStorage();
    }

    /**
     * @return ResponseInterface[]
     */
    public function getAll()
    {
        return iterator_to_array($this->responses);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            'before'   => ['clear'],
            'complete' => ['onComplete']
        ];
    }

    public function clear()
    {
        $this->responses->removeAll($this->responses);
    }

    /**
     * @param CompleteEvent $event
     */
    public function onComplete(CompleteEvent $event)
    {
        $response = $event->getResponse();

        if ($response && null !== $response->getRpcId()) {
            $this->responses->attach($response);
        }
    }
}
