<?php

namespace RemoteObjects\Test;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RemoteObjects\Encode\Encoder;
use RemoteObjects\Server;
use RemoteObjects\Encode\JsonRpc20Encoder;
use RemoteObjects\Transport\UnixSocketServer;

class UnixSocketTestServer
{
	public function run($socketPath, $target, Encoder $encoder)
	{
		$logger = new Logger('server');
		$logger->pushHandler(new StreamHandler(sys_get_temp_dir() . '/phpunit.log'));

		$transport = new UnixSocketServer($socketPath);
		$transport->setLogger($logger);

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
