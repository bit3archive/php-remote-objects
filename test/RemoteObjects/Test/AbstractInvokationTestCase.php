<?php

namespace RemoteObjects\Test;

use RemoteObjects\Server;
use RemoteObjects\Client;

abstract class AbstractInvokationTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return Server
	 */
	protected abstract function spawnServer();

	/**
	 * @return Server
	 */
	protected abstract function shutdownServer(Server $server);

	/**
	 * @return Client
	 */
	protected abstract function spawnClient();

	/**
	 * @return Client
	 */
	protected abstract function shutdownClient(Client $client);

	/**
	 * @covers \RemoteObjects\Client::invoke
	 * @covers \RemoteObjects\Server::handle
	 */
	public function testInvoke()
	{
		$pid = pcntl_fork();

		if ($pid == -1) {
			throw new \Exception('Forking unsupported');
		}
		else if ($pid) {
			try {
				$client = $this->spawnClient();

				$result = $client->invoke('reply', 'Hello World!');
				$this->assertEquals('Hello World!', $result);

				$this->shutdownClient($client);
			}
			catch(\Exception $e) {
				$this->shutdownClient($client);
				throw $e;
			}
		}
		else {
			try {
				$server = $this->spawnServer();
				$server->handle();
				$this->shutdownServer($server);
			}
			catch(\Exception $e) {
				$this->shutdownServer($server);
				throw $e;
			}
		}
	}

	/**
	 * @covers \RemoteObjects\Client::invoke
	 * @covers \RemoteObjects\Server::handle
	 */
	public function testInvalidMethod()
	{
		$pid = pcntl_fork();

		if ($pid == -1) {
			throw new \Exception('Forking unsupported');
		}
		else if ($pid) {
			try {
				$this->setExpectedException('Exception', 'Method not found', -32601);

				$client = $this->spawnClient();

				$client->invoke('unknownMethod');
				$this->shutdownClient($client);
			}
			catch(\Exception $e) {
				$this->shutdownClient($client);
				throw $e;
			}
		}
		else {
			try {
				$server = $this->spawnServer();
				$server->handle();
				$this->shutdownServer($server);
			}
			catch(\Exception $e) {
				$this->shutdownServer($server);
				throw $e;
			}
		}
	}
}
