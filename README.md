Remote objects invocation framework
===================================

Com'on, not another invocation framework?
Is there are not enough XML-RPC, JSON-RPC and similar implementations out there?
Yes, they are, but they are all not flexible enough and most do not provide any security features like encryption.

The benefit of RemoteObjects is to provide a remote method invocation framework,
that is secure (allow encryption), flexible (allow different transports like unix socket) and interoperable (support JSON-RPC 2.0 by default, but can also support XML-RPC or other standard).
Every component is exchangeable, if you like, you also able to invoke a method via SMS, or just use your own protocol instead of JSON-RPC. Everything is possible!

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

Security
--------

Sometimes you will increase security, but your transport layer does not support encryption (for example you cannot use HTTPS for some reason).
RemoteObjects provide a `RsaEncoder` that encrypt the data before transport and decode before evaluation.

Using the `RsaEncoder`, is really simple:
```php
$jsonEncoder = new RemoteObjects\Encode\JsonRpc20Encoder();
$encoder = new RemoteObjects\Encode\RsaEncoder(
	$jsonEncoder,
	'<add remote public key here>',
	'<add local private key here>'
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
