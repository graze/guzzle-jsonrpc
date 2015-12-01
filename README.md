# Guzzle JSON-RPC

[![Master branch build status][ico-build]][travis]
[![Published version][ico-package]][package]
[![PHP ~5.5][ico-engine]][lang]
[![MIT Licensed][ico-license]][license]

This library implements [JSON-RPC 2.0][jsonrpc] for the Guzzle HTTP client. We
try to support all commonly used versions of Guzzle including:
 - [GuzzleHTTP 6][guzzle] on [`master`][branch-master] branch, `^3.0` releases
 - [GuzzleHTTP 5][guzzle] on [`guzzle-5`][branch-5] branch, `^2.1` releases
 - [GuzzleHTTP 4][guzzle] on [`guzzle-4`][branch-4] branch, `2.0.*` releases
 - [Guzzle 3][guzzle-3] on [`guzzle-3`][branch-3] branch, `^1.0` releases

It can be installed in whichever way you prefer, but we recommend [Composer][package].
```json
{
    "require": {
        "graze/guzzle-jsonrpc": "~3.0"
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
$client->send($client->notification('method', ['key'=>'value']));

// Send a request that expects a response
$client->send($client->request(123, 'method', ['key'=>'value']));

// Send a batch of requests
$client->sendAll([
    $client->request(123, 'method', ['key'=>'value']),
    $client->request(456, 'method', ['key'=>'value']),
    $client->notification('method', ['key'=>'value'])
]);
```

### Async requests
Asynchronous requests are supported by making use of the
[Guzzle Promises][guzzle-promise] library; an implementation of
[Promises/A+][promise].
```php
<?php
use Graze\GuzzleHttp\JsonRpc\Client;

// Create the client
$client = Client::factory('http://localhost:8000');

// Send an async notification
$promise = $client->sendAsync($client->notification('method', ['key'=>'value']));
$promise->then(function () {
    // Do something
});

// Send an async request that expects a response
$promise = $client->sendAsync($client->request(123, 'method', ['key'=>'value']));
$promise->then(function ($response) {
    // Do something with the response
});

// Send a batch of requests
$client->sendAllAsync([
    $client->request(123, 'method', ['key'=>'value']),
    $client->request(456, 'method', ['key'=>'value']),
    $client->notification('method', ['key'=>'value'])
])->then(function ($responses) {
    // Do something with the list of responses
});
```

### Throw exception on RPC error
You can throw an exception if you receive an RPC error response by adding the
option `[rpc_error => true]` in the client constructor.
```php
<?php
use Graze\GuzzleHttp\JsonRpc\Client;
use Graze\GuzzleHttp\JsonRpc\Exception\RequestException;

// Create the client with the `rpc_error`
$client = Client::factory('http://localhost:8000', ['rpc_error'=>true]);

// Create a request
$request = $client->request(123, 'method', ['key'=>'value']);

// Send the request
try {
    $client->send($request);
} catch (RequestException $e) {
    die($e->getResponse()->getRpcErrorMessage());
}
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
[ico-engine]: http://img.shields.io/badge/php-~5.5-8892BF.svg?style=flat
[vagrant]: http://vagrantup.com
[jsonrpc]: http://jsonrpc.org/specification
[guzzle]: https://github.com/guzzle/guzzle
[promise]: https://promisesaplus.com
[guzzle-3]: https://github.com/guzzle/guzzle3
[guzzle-promise]: https://github.com/guzzle/promises
[branch-3]: https://github.com/graze/guzzle-jsonrpc/tree/guzzle-3
[branch-4]: https://github.com/graze/guzzle-jsonrpc/tree/guzzle-4
[branch-5]: https://github.com/graze/guzzle-jsonrpc/tree/guzzle-5
[branch-master]: https://github.com/graze/guzzle-jsonrpc
[license]: LICENSE
