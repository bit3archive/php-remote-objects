<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Test;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RemoteObjects\Encode\Encoder;
use RemoteObjects\Server;
use RemoteObjects\Transport\UnixSocketServer;

/**
 * Class UnixSocketTestServer
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Test
 * @api
 */
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
