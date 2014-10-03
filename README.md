# Guzzle JSON-RPC

[![Master branch build status][ico-build]][travis]
[![Published version][ico-package]][package]
[![PHP ~5.4][ico-engine]][lang]
[![MIT Licensed][ico-license]][license]

This library implements [JSON-RPC 2.0][jsonrpc] for the
[GuzzleHTTP 4.x client][guzzle]. For a version compatible with
[Guzzle 3.x][guzzle-3], use the [`guzzle-3` branch][branch-3] of this library.

It can be installed in whichever way you prefer, but we recommend [Composer][package].
```json
{
    "require": {
        "graze/guzzle-jsonrpc": "~2.0"
    }
}
```

## Documentation
```php
<?php
use Graze\GuzzleHttp\JsonRpc\Client;

// Create the client
$client = Client::factory('http://localhost:8000');

// Send a notification
$client->send($client->notification('method', ['key'=>'value']))

// Send a request that expects a response
$client->send($client->request(123, 'method', ['key'=>'value']));

// Send a batch of requests
$request->sendAll([
    $client->request(123, 'method', ['key'=>'value']),
    $client->request(456, 'method', ['key'=>'value']),
    $client->notification('method', ['key'=>'value'])
]);
```

## Contributing
We accept contributions to the source via Pull Request,
but passing unit tests must be included before it will be considered for merge.
```bash
$ composer install
$ make test
```

If you have [Vagrant][vagrant] installed, you can build our dev environment to
assist development. The repository will be mounted in `/srv`.
```bash
$ vagrant up
$ vagrant ssh

Welcome to Ubuntu 12.04 LTS (GNU/Linux 3.2.0-23-generic x86_64)
$ cd /srv
```

### License
The content of this library is released under the **MIT License** by
**Nature Delivered Ltd**.<br/> You can find a copy of this license at
http://www.opensource.org/licenses/mit or in [`LICENSE`][license]

<!-- Links -->
[travis]: https://travis-ci.org/graze/guzzle-jsonrpc
[lang]: http://php.net
[package]: https://packagist.org/packages/graze/guzzle-jsonrpc
[ico-license]: http://img.shields.io/packagist/l/graze/guzzle-jsonrpc.svg?style=flat
[ico-package]: http://img.shields.io/packagist/v/graze/guzzle-jsonrpc.svg?style=flat
[ico-build]: http://img.shields.io/travis/graze/guzzle-jsonrpc/master.svg?style=flat
[ico-engine]: http://img.shields.io/badge/php-~5.4-8892BF.svg?style=flat
[vagrant]: http://vagrantup.com
[jsonrpc]: http://jsonrpc.org/specification
[guzzle]: https://github.com/guzzle/guzzle
[guzzle-3]: https://github.com/guzzle/guzzle3
[branch-3]: https://github.com/graze/guzzle-jsonrpc/tree/guzzle-3
[license]: LICENSE
