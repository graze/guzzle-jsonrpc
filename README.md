# Guzzle JSON-RPC

[![Master branch build status][ico-build]][travis]
[![Coverage Status][ico-coverage]][coverage]
[![Quality Score][ico-quality]][quality]
[![Published version][ico-package]][package]
[![PHP ~5.4][ico-engine]][lang]
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
        "graze/guzzle-jsonrpc": "^2.1"
    }
}
```

```shell
~ $ composer require graze/guzzle-jsonrpc:^2.1
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
$request->sendAll([
    $client->request(123, 'method', ['key'=>'value']),
    $client->request(456, 'method', ['key'=>'value']),
    $client->notification('method', ['key'=>'value'])
]);
```

### Throw exception on RPC error

You can throw an exception if you receive an RPC error response by attaching a
subscriber to either the client or the request. You probably won't want to do so
with batch requests as the exception will only include the first bad response in
your batch.

```php
<?php
use Graze\GuzzleHttp\JsonRpc\Client;
use Graze\GuzzleHttp\JsonRpc\Exception\RequestException;
use Graze\GuzzleHttp\JsonRpc\Subscriber\ErrorSubscriber;

// Create the client
$client = Client::factory('http://localhost:8000');

// Create a request and attach the error subscriber
$request = $client->request(123, 'method', ['key'=>'value']);
$request->getEmitter()->attach(new ErrorSubscriber());

// Send the request
try {
    $client->send($request);
} catch (RequestException $e) {
    die($e->getResponse()->getRpcErrorMessage());
}
```

### Contributing

We accept contributions to the source via Pull Request,
but passing unit tests must be included before it will be considered for merge.

```bash
~ $ make deps
~ $ make lint test
```

### License

The content of this library is released under the **MIT License** by
**Nature Delivered Ltd**.

You can find a copy of this license at
[MIT][mit] or in [`LICENSE`][license]

<!-- Links -->
[mit]: http://www.opensource.org/licenses/mit
[travis]: https://travis-ci.org/graze/guzzle-jsonrpc
[lang]: http://php.net
[package]: https://packagist.org/packages/graze/guzzle-jsonrpc
[coverage]: https://scrutinizer-ci.com/g/graze/guzzle-jsonrpc/guzzle-5/code-structure
[quality]: https://scrutinizer-ci.com/g/graze/guzzle-jsonrpc/guzzle-5
[ico-license]: http://img.shields.io/packagist/l/graze/guzzle-jsonrpc.svg?style=flat
[ico-package]: http://img.shields.io/packagist/v/graze/guzzle-jsonrpc.svg?style=flat
[ico-build]: http://img.shields.io/travis/graze/guzzle-jsonrpc/guzzle-5.svg?style=flat
[ico-engine]: http://img.shields.io/badge/php-~5.4-8892BF.svg?style=flat
[ico-coverage]: https://img.shields.io/scrutinizer/coverage/g/graze/guzzle-jsonrpc/guzzle-5.svg?style=flat
[ico-quality]: https://img.shields.io/scrutinizer/g/graze/guzzle-jsonrpc/guzzle-5.svg?style=flat
[vagrant]: http://vagrantup.com
[jsonrpc]: http://jsonrpc.org/specification
[guzzle]: https://github.com/guzzle/guzzle
[guzzle-3]: https://github.com/guzzle/guzzle3
[branch-3]: https://github.com/graze/guzzle-jsonrpc/tree/guzzle-3
[branch-4]: https://github.com/graze/guzzle-jsonrpc/tree/guzzle-4
[branch-5]: https://github.com/graze/guzzle-jsonrpc/tree/guzzle-5
[branch-master]: https://github.com/graze/guzzle-jsonrpc
[license]: LICENSE
