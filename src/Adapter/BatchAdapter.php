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
namespace Graze\GuzzleHttp\JsonRpc\Adapter;

use Graze\GuzzleHttp\JsonRpc\Message\RequestInterface;
use Graze\GuzzleHttp\JsonRpc\Subscriber\BatchSubscriber;
use GuzzleHttp;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Adapter\ParallelAdapterInterface;
use GuzzleHttp\Adapter\TransactionInterface;
use GuzzleHttp\Message\MessageFactoryInterface;
use Iterator;
use LimitIterator;
use LogicException;

/**
 * The Batch Adapter takes a list of Transactions, extracts the RPC fields from
 * each Request body and creates a new Request with all of the data combined.
 * This new Request object is then sent through the client and the Response is
 * divided up into individual Response objects.
 *
 * @link http://www.jsonrpc.org/specification#batch
 */
class BatchAdapter implements ParallelAdapterInterface
{
    /**
     * @var MessageFactoryInterface
     */
    protected $factory;

    /**
     * @param MessageFactoryInterface $factory
     */
    public function __construct(MessageFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function sendAll(Iterator $transactions, $max)
    {
        $limited = new LimitIterator($transactions, 0, $max);
        $client  = $this->getClientFromTransactions($limited);
        $request = $this->createRequest($client, $limited);

        $client->send($request);
    }

    /**
     * @param  ClientInterface  $client
     * @param  Iterator         $transactions
     * @return RequestInterface
     */
    protected function createRequest(ClientInterface $client, Iterator $transactions)
    {
        $data = ['jsonrpc' => $this->getDataFromTransactions($transactions)];

        $request = $this->factory->createRequest(RequestInterface::BATCH, $client->getBaseUrl(), $data);
        $request->getEmitter()->attach(new BatchSubscriber($transactions));

        return $request;
    }

    /**
     * @param  Iterator        $transactions
     * @return ClientInterface
     * @throws LogicException  If the client isn't the same in all transactions
     */
    protected function getClientFromTransactions(Iterator $transactions)
    {
        $client = null;

        foreach ($transactions as $transaction) {
            if (!$client) {
                $client = $transaction->getClient();
            } elseif ($client !== $transaction->getClient()) {
                throw new LogicException('Batch requests must be created by the same client');
            }
        }

        return $client;
    }

    /**
     * @param  Iterator $transactions
     * @return array
     */
    protected function getDataFromTransactions(Iterator $transactions)
    {
        return array_map(function (TransactionInterface $transaction) {
            $request = $transaction->getRequest();
            $data = GuzzleHttp\json_decode((string) $request->getBody());

            return $data;
        }, iterator_to_array($transactions));
    }
}
