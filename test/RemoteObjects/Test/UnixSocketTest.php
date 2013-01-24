<?php

namespace RemoteObjects\Test;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RemoteObjects\Server;
use RemoteObjects\Client;
use RemoteObjects\Encode\JsonRpc20Encoder;
use RemoteObjects\Transport\UnixSocketServer;
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
		$logger = new Logger('phpunit');
		$logger->pushHandler(new StreamHandler('php://stderr'));

		$transport = new UnixSocketServer($this->serverSocket);
		$transport->setLogger($logger);

		$encoder = new JsonRpc20Encoder();
		$encoder->setLogger($logger);

		$server = new Server(
			$transport,
			$encoder,
			new EchoObject()
		);
		$server->setLogger($logger);

		return $server;
	}

	/**
	 * @return Server
	 */
	protected function shutdownServer(Server $server)
	{
		$server->getTransport()->close();
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
	protected function shutdownClient(Client $client)
	{
		$client->getTransport()->close();
	}
}