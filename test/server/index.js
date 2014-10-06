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
var rpc = require('jayson');

var server = rpc.server({
    concat: function concatFn(foo, bar, done) {
        if (arguments.length === 3) {
            done(null, foo + bar);
        } else {
            done(this.error(-32602));
        }
    },
    sum: function sumFn(foo, bar, done) {
        if (arguments.length === 3) {
            done(null, foo + bar);
        } else {
            done(this.error(-32602));
        }
    },
    notify: function notifyFn(foo, done) {
        done();
    },
    foo: function fooFn(done) {
        if (arguments.length === 1) {
            done(null, 'foo');
        } else {
            done(this.error(-32602));
        }
    }
});

server.http().listen(8000);
