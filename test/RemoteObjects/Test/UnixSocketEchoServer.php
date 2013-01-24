<?php

namespace RemoteObjects\Test;

require(__DIR__ . '/../../../vendor/autoload.php');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RemoteObjects\Server;
use RemoteObjects\Encode\JsonRpc20Encoder;
use RemoteObjects\Transport\UnixSocketServer;

class EchoServer
{
	public function run($socketPath)
	{
		$logger = new Logger('phpunit');
		$logger->pushHandler(new StreamHandler('php://stderr'));

		$transport = new UnixSocketServer($socketPath);
		$transport->setLogger($logger);

		$encoder = new JsonRpc20Encoder();
		$encoder->setLogger($logger);

		$server = new Server(
			$transport,
			$encoder,
			new EchoObject()
		);
		$server->setLogger($logger);

		$server->handle();

		$transport->close();
	}
}

global $argv;

$server = new EchoServer();
$server->run($argv[1]);
