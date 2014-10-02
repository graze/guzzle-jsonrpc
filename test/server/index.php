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

use JsonRpc\Server;

require __DIR__ . '/../../vendor/autoload.php';

$server = new Server(new RpcApplication());
$server->setObjectsAsArrays();
$server->receive();

class RpcApplication
{
    public $error;

    public function concat($foo, $bar)
    {
        if (!is_string($foo) || !is_string($bar)) {
            $this->error = 'Can\'t concatenate non string values';
            return;
        }

        return $foo . $bar;
    }

    public function sum($foo, $bar)
    {
        if (!is_int($foo) || !is_int($bar)) {
            $this->error = 'Can\'t sum non integer values';
            return;
        }

        return $foo + $bar;
    }

    public function notify($foo)
    {
        if (!is_bool($foo)) {
            $this->error = 'Can\'t notify with non boolean value';
        }
    }

    public function foo()
    {
        return 'foo';
    }
}
