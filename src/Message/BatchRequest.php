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

use LogicException;
use RuntimeException;

class BatchRequest extends AbstractRequest
{
    /**
     * @var Request[]
     */
    protected $requests = [];

    /**
     * @return Response[]
     */
    public function send()
    {
        $responses = [];
        $this->setBody($this->jsonEncode($this->joinRequests($this->requests)));

        $batch = $this->sendEntityEnclosingRequest();
        $map = $this->mapResponses($batch->json());

        foreach ($this->requests as $request) {
            $id = $request->getRpcId();

            if (null !== $id) {
                if (!isset($map[$id])) {
                    throw new RuntimeException('Response with ID "' . $id . '" expected but not received.');
                }

                $data = $map[$id];

                if (!array_key_exists('result', $data)) {
                    $responses[] = new ErrorResponse($batch, $data);
                } else {
                    $responses[] = new Response($batch, $data);
                }
            }
        }

        return $responses;
    }

    /**
     * @param RequestInterface $request
     */
    public function addRequest(RequestInterface $request)
    {
        $this->requests[] = $request;
    }

    /**
     * @param RequestInterface[] $requests
     */
    public function setRequests(array $requests)
    {
        $this->requests = [];

        foreach ($requests as $request) {
            $this->addRequest($request);
        }
    }

    /**
     * @param Request[] $requests
     * @return array
     */
    protected function joinRequests(array $requests)
    {
        $ids = [];

        return array_map(function (RequestInterface $request) use (&$ids) {
            $id = $request->getRpcId();

            if (null !== $id) {
                if (isset($ids[$id])) {
                    throw new LogicException('Duplicate request ID "' . $id . '".');
                }

                $ids[$id] = true;
            }

            return $request->getRpcData();
        }, $requests);
    }

    /**
     * @param array $responses
     * @return array
     */
    protected function mapResponses(array $responses)
    {
        $map = [];

        foreach ($responses as $response) {
            if (isset($response['id'])) {
                $map[$response['id']] = $response;
            }
        }

        return $map;
    }
}
