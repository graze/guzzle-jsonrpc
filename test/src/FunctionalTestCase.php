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
use PHPUnit_Framework_TestCase as TestCase;

class FunctionalTestCase extends TestCase
{
    /**
     * @var string
     */
    protected $defaultUrl = 'http://0.0.0.0:8000';

    /**
     * @param  string $url
     * @param  array  $config
     *
     * @return Client
     */
    public function createClient($url = null, array $config = [])
    {
        return Client::factory($url ?: $this->defaultUrl, $config);
    }
}
