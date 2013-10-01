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
use Symfony\Component\Process\Process;
use RemoteObjects\Server;
use RemoteObjects\Client;
use RemoteObjects\Transport\UnixSocketClient;

/**
 * Class UnixSocketTest
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Test
 * @api
 */
class UnixSocketTest extends AbstractInvocationTestCase
{
	/**
	 * @var string
	 */
	protected $serverSocket = null;

	/**
	 * @var string
	 */
	protected $clientSocket = null;

	protected $server = null;

	protected function setUp()
	{
		$path = tempnam(sys_get_temp_dir(), 'socket_');
		unlink($path);

		$this->serverSocket = $path . '.server';
		$this->clientSocket = $path . '.client';
	}

	/**
	 * @param string $target
	 * @param Logger $logger
	 * @return Server
	 */
	protected function spawnServer($target, $logger, Encoder $encoder)
	{
		$pid = pcntl_fork();

		if ($pid == -1) {
			throw new \Exception('Could not spawn the server!');
		}
		else if ($pid) {
			return $pid;
		}
		else {
			$server = new UnixSocketTestServer();
			$server->run($this->serverSocket, $target, $encoder);
			exit;
		}
	}

	/**
	 * @return Server
	 */
	protected function shutdownServer($server)
	{
		pcntl_waitpid($server, $status);
	}

	/**
	 * @return Client
	 */
	protected function spawnClient($logger, Encoder $encoder)
	{
		// wait for server
		do {
			sleep(1);
		} while(!file_exists($this->serverSocket));

		$transport = new UnixSocketClient($this->clientSocket, $this->serverSocket);
		$transport->setLogger($logger);

		$encoder->setLogger($logger);

		return new Client(
			$transport,
			$encoder
		);
	}

	/**
	 * @return Client
	 */
	protected function shutdownClient($client)
	{
		$client->getTransport()->close();
	}
}