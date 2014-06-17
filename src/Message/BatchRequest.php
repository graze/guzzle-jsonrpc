<?php
/*
 * This file is part of Guzzle JsonRpc
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

use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Http\Message\RequestInterface;
use LogicException;
use RuntimeException;

class BatchRequest extends EntityEnclosingRequest
{
    /**
     * @var Request[]
     */
    protected $requests = array();

    /**
     * @param RequestInterface $request
     * @param Request[] $requests
     */
    public function __construct(RequestInterface $request, array $requests)
    {
        parent::__construct($request->getMethod(), $request->getUrl(), $request->getHeaders());

        $this->setClient($request->getClient());
        foreach ($requests as $request) {
            $this->addRequest($request);
        }
    }

    /**
     * @return Response
     */
    public function send()
    {
        $responses = array();
        $this->setBody($this->jsonEncode($this->joinRequests($this->requests)));

        $batch = parent::send();
        $map = $this->mapResponses($batch->json());

        foreach ($this->requests as $request) {
            $id = $request->getRpcField('id');

            if (null !== $id) {
                if (!isset($map[$id])) {
                    throw new RuntimeException('Response with ID "' . $id . '" expected but not received.');
                }

                $data = $map[$id];
                $responses[] = array_key_exists('result', $data) ? new Response($batch, $data) : new ErrorResponse($batch, $data);
            }
        }

        return $responses;
    }

    /**
     * @param Request $request
     */
    public function addRequest(Request $request)
    {
        $this->requests[] = $request;
    }

    /**
     * @param Request[] $requests
     * @return array
     */
    protected function joinRequests(array $requests)
    {
        $ids = array();

        return array_map(function(Request $request) use(&$ids) {
            if ($request->getRpcField('id') !== null) {
                if (isset($ids[$request->getRpcField('id')])) {
                    throw new LogicException('Duplicate request ID "' . $request->getRpcField('id') . '".');
                }

                $ids[$request->getRpcField('id')] = true;
            }

            return $request->getRpcFields();
        }, $requests);
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }

    /**
     * @param array $responses
     * @return array
     */
    protected function mapResponses(array $responses)
    {
        $map = array();

        foreach ($responses as $response) {
            if (isset($response['id'])) {
                $map[$response['id']] = $response;
            }
        }

        return $map;
    }
}
