<?php

namespace RemoteObjects\Test;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Process\Process;
use RemoteObjects\Server;
use RemoteObjects\Client;
use RemoteObjects\Encode\JsonRpc20Encoder;
use RemoteObjects\Transport\UnixSocketClient;

class UnixSocketTest extends AbstractInvokationTestCase
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
	 * @return Server
	 */
	protected function spawnServer()
	{
		$server = new Process(
			'php ' . escapeshellarg(__DIR__ . '/UnixSocketEchoServer.php') . ' ' . escapeshellarg($this->serverSocket),
			__DIR__
		);
		$server->setTimeout(15);
		$server->start();
		return $server;
	}

	/**
	 * @return Server
	 */
	protected function shutdownServer($server)
	{
		$server->wait();
	}

	/**
	 * @return Client
	 */
	protected function spawnClient()
	{
		$logger = new Logger('phpunit');
		$logger->pushHandler(new StreamHandler('php://stderr'));

		// wait for server
		do {
			sleep(1);
		} while(!file_exists($this->serverSocket));

		$transport = new UnixSocketClient($this->clientSocket, $this->serverSocket);
		$transport->setLogger($logger);

		$encoder = new JsonRpc20Encoder();
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