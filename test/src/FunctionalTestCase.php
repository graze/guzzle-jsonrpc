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

namespace Graze\GuzzleHttp\JsonRpc\Test;

use Graze\GuzzleHttp\JsonRpc\Client;

class FunctionalTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $defaultUrl = 'http://node:80';

    /**
     * @param  string|null $url
     * @param  array       $config
     *
     * @return Client
     */
    public function createClient($url = null, array $config = [])
    {
        return Client::factory($url ?: $this->defaultUrl, $config);
    }
}
