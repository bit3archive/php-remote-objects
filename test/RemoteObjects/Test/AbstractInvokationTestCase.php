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
	protected abstract function shutdownServer($server);

	/**
	 * @return Client
	 */
	protected abstract function spawnClient();

	/**
	 * @return Client
	 */
	protected abstract function shutdownClient($client);

	/**
	 * @covers \RemoteObjects\Client::invoke
	 * @covers \RemoteObjects\Server::handle
	 */
	public function testInvoke()
	{
		try {
			$server = $this->spawnServer();
			$client = $this->spawnClient();

			$result = $client->invoke('reply', 'Hello World!');
			$this->assertEquals('Hello World!', $result);

			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch(\Exception $e) {
			$this->shutdownClient($client);
			$this->shutdownServer($server);
			throw $e;
		}
	}

	/**
	 * @covers \RemoteObjects\Client::invoke
	 * @covers \RemoteObjects\Server::handle
	 */
	public function testInvalidMethod()
	{
		try {
			$server = $this->spawnServer();
			$this->setExpectedException('Exception', 'Method not found', -32601);

			$client = $this->spawnClient();

			$client->invoke('unknownMethod');
			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch(\Exception $e) {
			$this->shutdownClient($client);
			$this->shutdownServer($server);
			throw $e;
		}
	}

	/**
	 * @covers \RemoteObjects\Client::castAsRemoteObject
	 */
	public function testClientCast()
	{
		try {
			$server = $this->spawnServer();
			$client = $this->spawnClient();

			/** @var EchoInterface $echo */
			$echo = $client->castAsRemoteObject('RemoteObjects\Test\EchoInterface');

			$this->assertTrue(
				$echo instanceof \RemoteObjects\RemoteObject
			);
			$this->assertTrue(
				$echo instanceof EchoInterface
			);
			$this->assertEquals(
				'Hello World!',
				$echo->reply('Hello World!')
			);

			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch(\Exception $e) {
			$this->shutdownClient($client);
			$this->shutdownServer($server);
			throw $e;
		}
	}
}
