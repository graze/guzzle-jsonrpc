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

use Graze\GuzzleHttp\JsonRpc;
use Graze\GuzzleHttp\JsonRpc\ClientInterface;
use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Message\ResponseInterface;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Stream\Stream;
use Iterator;

class BatchSubscriber implements SubscriberInterface
{
    /**
     * @var Iterator
     */
    protected $transactions;

    /**
     * @param Iterator $transactions
     */
    public function __construct(Iterator $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            'before'   => ['onBefore'],
            'complete' => ['onComplete'],
            'error'    => ['onError']
        ];
    }

    /**
     * @param BeforeEvent $event
     */
    public function onBefore(BeforeEvent $event)
    {
        foreach ($this->transactions as $transaction) {
            RequestEvents::emitBefore($transaction);
        }
    }

    /**
     * @param CompleteEvent $event
     */
    public function onComplete(CompleteEvent $event)
    {
        $response = $event->getResponse();
        $data = $response->json();

        foreach ($this->transactions as $transaction) {
            $request = $transaction->getRequest();
            $childResponse = $this->createChildResponse($request, $response, $data);
            $transaction->setResponse($childResponse);

            RequestEvents::emitComplete($transaction);
        }
    }

    /**
     * @param ErrorEvent $event
     */
    public function onError(ErrorEvent $event)
    {
        $response = $event->getResponse();
        $exception = $event->getException();

        foreach ($this->transactions as $transaction) {
            $request = $transaction->getRequest();
            if ($request instanceof RequestInterface && null !== $request->getRpcId()) {
                $transaction->setResponse($response);
            }

            RequestEvents::emitError($transaction, $exception);
        }
    }

    /**
     * @param  RequestInterface  $request
     * @param  ResponseInterface $response
     * @param  array             $data
     * @return ResponseInterface
     */
    protected function createChildResponse(
        RequestInterface $request,
        ResponseInterface $response,
        array $data
    ) {
        $childResponse = null;
        foreach ($data as $result) {
            if (isset($result['id']) && $request->getRpcId() === $result['id']) {
                $childResponse = clone $response;
                $childResponse->setBody(Stream::factory(JsonRpc\json_encode($result)));
            }
        }

        if (!isset($childResponse)) {
            $childResponse = clone $response;
            $childResponse->setBody(Stream::factory(JsonRpc\json_encode([
                'jsonrpc' => ClientInterface::SPEC,
                'id' => $request->getRpcId(),
                'error' => [
                    'code' => -32603,
                    'message' => 'Result expected but none given in server response'
                ]
            ])));
        }

        return $childResponse;
    }
}
