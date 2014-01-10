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

interface ResponseInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getVersion();
}
