<?php

namespace RemoteObjects\Test;

require(__DIR__ . '/../../../vendor/autoload.php');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RemoteObjects\Server;
use RemoteObjects\Encode\JsonRpc20Encoder;
use RemoteObjects\Transport\UnixSocketServer;

class UnixSocketTestServer
{
	public function run($socketPath, $target)
	{
		$logger = new Logger('server');
		$logger->pushHandler(new StreamHandler(sys_get_temp_dir() . '/phpunit.log'));

		$transport = new UnixSocketServer($socketPath);
		$transport->setLogger($logger);

		$encoder = new JsonRpc20Encoder();
		$encoder->setLogger($logger);

		$server = new Server(
			$transport,
			$encoder,
			$target
		);
		$server->setLogger($logger);

		$server->handle();

		$transport->close();
	}
}

global $argv;

$server = new UnixSocketTestServer();
$server->run($argv[1], unserialize($argv[2]));
