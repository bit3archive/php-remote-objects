Remote object method invocation framework
=========================================

[![Build Status](https://travis-ci.org/bit3/php-RemoteObjects.png?branch=master)](https://travis-ci.org/bit3/php-RemoteObjects)

Com'on, not another method invocation framework?
Aren't there enough XML-RPC, JSON-RPC and similar implementations out there?
Yes, there are, but they are not flexible enough and most of them do not provide any security features like encryption.

The benefit of RemoteObjects is to provide a remote method invocation framework,
that is secure (allow encryption), flexible (allow different transports like unix socket) and interoperable (support JSON-RPC 2.0 by default, but can also support XML-RPC or other protocols).
Every component is exchangeable, if you like, you are also able to invoke a method via SMS as transport layer, or just use your own protocol instead of JSON-RPC. Everything is possible!

Or in other words: This framework combines the pros of all the others ;-)

How it works
------------

```
user -> Client::invoke($method, $params)
         \
          \--> Encoder::encodeMethod($method, $params)
                     /
        $request  <-/
         \
          \--> Transport\Client::request($request)
                |
                |
       HTTP or something else :-)
                |
                V
              Transport\Server::receive()
                     /
        $request  <-/
         \
          \--> Encoder::decodeMethod($request)
                               /
        [$method, $params]  <-/
         \
          \--> Server::invokeArgs($method, $params)
                \
                 \--> $target->$method($params..)
                            /
               $result  <--/
                \
                 \--> Encoder::encodeResult($result)
                             /
               $response <--/
                \
                 \--> Transport\Server:respond($response)
                       |
                       |
                All the way back
                       |
                       V
                      Transport\Client::request(...)
                      /
        $response <--/
         \
          \--> Encoder::decodeResult($response)
                    /
        $result <--/
         /
user <--/
```

Usage example
-------------

A usage example using JSON-RPC 2.0 as protocol and a unix socket as transport layer.

`server.php`
```php
class RemoteObject
{
	public function reply($name)
	{
		return 'Hello ' . $name;
	}
}

$transport = new RemoteObjects\Transport\UnixSocketServer('/tmp/socket.server');

$encoder = new RemoteObjects\Encode\JsonRpc20Encoder();

$target = new RemoteObject();

$server = new RemoteObjects\Server(
	$transport,
	$encoder,
	$target
);
$server->handle();

```

`client.php`
```php

$transport = new RemoteObjects\Transport\UnixSocketClient('/tmp/socket.client', '/tmp/socket.server');

$encoder = new RemoteObjects\Encode\JsonRpc20Encoder();

$server = new RemoteObjects\Client(
	$transport,
	$encoder
);
$result = $server->invoke('reply', 'Tristan');
echo $result; // -> "Hello Tristan"
```

Chaining and deep structure
---------------------------

Inspired from another JSON-RPC library, RemoteObjects allow complex structures on the server side.
Sometimes you need a complex remote api, but you won't to overload your remote object class.
Instead of creating multiple endpoints, it's possible to create multiple "named" remote objects.

Creating named objects is realy easy:

```php
$server = new RemoteObjects\Server(
	$transport,
	$encoder,
	array(
		'a' => $targetA,
		'b' => $targetB
	)
);
```

To call methods from `$targetA` or `$targetB` you just have to prefix the method name with the object name, concatenated with a dot.

```php
$client->invoke('a.methodA'); // invoke $targetA->methodA();
$client->invoke('b.methodB'); // invoke $targetB->methodB();
```

It is also possible to make bigger, more complex and multidimensional structures.

```php
$server = new RemoteObjects\Server(
	$transport,
	$encoder,
	array(
		'a' => array(
			'one' => $targetOne,
			'two' => array(
				'I' => $targetI,
				'II' => $targetII
			)
		),
		'b' => $targetB
	)
);
```

To call `$targetII->method()` for example, the method name will be `a.two.II.method`.

Hint: You can also use `ArrayAccess` compatible objects, instead of arrays!

For better and more programmatic chaining, is is possible to access the structure by virtual attributes of the `RemoteObject` objects.
Accessing a property of `RemoteObject` will give you a new `RemoteObject` instance to this named path (similar to `Client::getRemoteObject(<name>)`).

According to the previous example, access to `a.two.II.method` is also possible this way:

```php
$result = $client
	->castAsRemoteObject()
	->a
	->two
	->II
	->method();
```

Lazy objects
------------

In the last chapter, you read about chaining and deep structures.
But if your API grows up, creating all target objects may waste a lot of resources.
Instead of using arrays or `ArrayAccess` objects, you can make a class with getters for the named objects.

```php
class Objects
{
	public function getA()
	{
		return new TargetA();
	}

	public function getB()
	{
		return new TargetB();
	}
}
```

And then use this object as target.

```php

$server = new RemoteObjects\Server(
	$transport,
	$encoder,
	new Objects()
);
```

To access methods from `TargetA` or `TargetB` it is the same as before,
use `a.methodName` to invoke `TargetA::methodName` or `b.methodName` to invoke `TargetB::methodName`.

Remote object accessors
-----------------------

RemoteObjects allow to create `RemoteObject` objects to directly call the methods on it.

On the server side:

```php
$server = new RemoteObjects\Server(
	$transport,
	$encoder,
	array(
		'a' => $targetA,
		'b' => $targetB
	)
);
```

On the client side:

```php
$remoteA = $client->getRemoteObject('a');
$remoteB = $client->getRemoteObject('b');
```

Now `$remoteA` is an accessor to `$targetA` and `$remoteB` to `$targetB`.
Keep in mind that `$remoteA` and `$remoteB` are just proxies without any methods.
Every method is dynamically passed to the server side.

If you don't have named objects on the server side, you can also pack the complete client object as `RemoteObject`.

```php
$remote = $client->castAsRemoteObject();
```

Now every call `$remote->method($arg1, $arg2, ...)` will directly passed to `$client->invokeArgs('method', [$arg1, $arg2, ...])`.

Type mapping
------------

One big problem of remote method invocation, and the previous shown method is the nescience of the remote methods.
Every object you get with `Client::getRemoteObject` or `Client::castAsRemoteObject` is just a primitive proxy, without any methods.
You are unable to use `method_exists` or `ReflectionClass` to "inspect" the object and its methods.

To solve this problem, RemoteObjects allow you to specify a type, to use as `RemoteObject`.
Just specify your type, when calling `Client::getRemoteObject` or `Client::castAsRemoteObject`.

```php
interface MyRemote
{
	public function remoteMethod();
}

$remote = $client->castAsRemoteObject('MyRemote');
```

Since version 1.2 you can also use a class.

```php
class MyRemote
{
	public function remoteMethod()
	{
		// ...
	}
}

$remote = $client->castAsRemoteObject('MyRemote');
```

The object `$remote` **is** an instance of `MyRemote`, but it is also an instance of `RemoteObjects\RemoteObject`.

How this works?
Internally a virtual temporary proxy class is generated, named `RemoteProxies\__WS__\MyRemote` (the namespace is prefixed, in previous version the class name was suffixed) that implements the interface.

With this technique you can make nearly every object remote and pass the object to other methods without type hint mismatch.
All you need is an interface.

Known limitations
-----------------

* Pass parameters by reference is impossible for remote objects.
* Parameters that are unable to be serialized cannot be transported to the remote endpoint.

Security
--------

Sometimes you want to increase security, but your transport layer does not support encryption (for example you cannot use HTTPS for some reason).
RemoteObjects provides an `AesEncoder` and a `RsaEncoder` that encrypt the data before transport and decode before evaluation.

Using the `AesEncoder` or `RsaEncoder`, is really simple.

### AES

Server and client:
```php
$jsonEncoder = new RemoteObjects\Encode\JsonRpc20Encoder();
$encoder = new RemoteObjects\Encode\AesEncoder(
	$jsonEncoder,
	'<add your pre-shared key here>'
);
```

### RSA

Server:
```php
$jsonEncoder = new RemoteObjects\Encode\JsonRpc20Encoder();
$encoder = new RemoteObjects\Encode\RsaEncoder(
	$jsonEncoder,
	'<add client public key here>',
	'<add server private key here>'
);
```

Client:
```php
$jsonEncoder = new RemoteObjects\Encode\JsonRpc20Encoder();
$encoder = new RemoteObjects\Encode\RsaEncoder(
	$jsonEncoder,
	'<add server public key here>',
	'<add client private key here>'
);
```

Logging
-------

Monolog is supported, but not required.
You can add a logger to each transport, encoder or server/client object to log errors and debug informations.

```php
$logger = new \Monolog\Logger('RemoteObjects');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::ERROR));

$transport->setLogger($logger);

$encoder->setLogger($logger);

$server->setLogger($logger);
// or
$client->setLogger($logger);
```
