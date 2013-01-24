<?php

namespace RemoteObjects\Test;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use RemoteObjects\Server;
use RemoteObjects\Client;
use RemoteObjects\RemoteObject;

abstract class AbstractInvocationTestCase extends \PHPUnit_Framework_TestCase
{
	protected function getLogger($name)
	{
		$logger = new Logger($name);
		$logger->pushHandler(new StreamHandler(sys_get_temp_dir() . '/phpunit.log'));
		return $logger;
	}

	/**
	 * @return Server
	 */
	protected abstract function spawnServer($target, $logger);

	/**
	 * @return Server
	 */
	protected abstract function shutdownServer($server);

	/**
	 * @return Client
	 */
	protected abstract function spawnClient($logger);

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
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->addDebug('--- InvocationTestCase::testInvoke() ---');

			$server = $this->spawnServer(new EchoObject(), $serverLogger);
			$client = $this->spawnClient($clientLogger);

			$result = $client->invoke('reply', 'Hello World!');
			$this->assertEquals('Hello World!', $result);

			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch (\Exception $e) {
			if (isset($client)) {
				try {
					$this->shutdownClient($client);
				}
				catch (\Exception $e_) {
					$clientLogger->addError($e->getMessage());
				}
			}
			if (isset($server)) {
				try {
					$this->shutdownServer($server);
				}
				catch (\Exception $e_) {
					$serverLogger->addError($e->getMessage());
				}
			}
			throw $e;
		}
	}

	/**
	 * @covers \RemoteObjects\Client::invoke
	 * @covers \RemoteObjects\Server::handle
	 */
	public function testInvalidMethod()
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->addDebug('--- InvocationTestCase::testInvalidMethod() ---');

			$server = $this->spawnServer(new EchoObject(), $serverLogger);
			$this->setExpectedException('Exception', 'Method not found', -32601);

			$client = $this->spawnClient($clientLogger);

			$client->invoke('unknownMethod');
			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch (\Exception $e) {
			if (isset($client)) {
				try {
					$this->shutdownClient($client);
				}
				catch (\Exception $e_) {
					$clientLogger->addError($e->getMessage());
				}
			}
			if (isset($server)) {
				try {
					$this->shutdownServer($server);
				}
				catch (\Exception $e_) {
					$serverLogger->addError($e->getMessage());
				}
			}
			throw $e;
		}
	}

	/**
	 * @covers \RemoteObjects\Client::castAsRemoteObject
	 */
	public function testClientCast()
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->addDebug('--- InvocationTestCase::testClientCast() ---');

			$server = $this->spawnServer(new EchoObject(), $serverLogger);
			$client = $this->spawnClient($clientLogger);

			/** @var EchoInterface $echo */
			$echo = $client->castAsRemoteObject('RemoteObjects\Test\EchoInterface');

			$this->assertTrue(
				$echo instanceof RemoteObject
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
		catch (\Exception $e) {
			if (isset($client)) {
				try {
					$this->shutdownClient($client);
				}
				catch (\Exception $e_) {
					$clientLogger->addError($e->getMessage());
				}
			}
			if (isset($server)) {
				try {
					$this->shutdownServer($server);
				}
				catch (\Exception $e_) {
					$serverLogger->addError($e->getMessage());
				}
			}
			throw $e;
		}
	}

	/**
	 * @covers \RemoteObjects\Client::getRemoteObject
	 */
	public function testNamedRemote()
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->addDebug('--- InvocationTestCase::testNamedRemote() ---');

			$server = $this->spawnServer(
				array(
					 'echo' => new EchoObject()
				),
				$serverLogger
			);
			$client = $this->spawnClient($clientLogger);

			/** @var EchoInterface $echo */
			$echo = $client->getRemoteObject('echo', 'RemoteObjects\Test\EchoInterface');

			$this->assertTrue(
				$echo instanceof RemoteObject
			);
			$this->assertTrue(
				$echo instanceof EchoInterface
			);
			$this->assertAttributeEquals(
				'echo',
				'path',
				$echo
			);
			$this->assertEquals(
				'Hello World!',
				$echo->reply('Hello World!')
			);

			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch (\Exception $e) {
			if (isset($client)) {
				try {
					$this->shutdownClient($client);
				}
				catch (\Exception $e_) {
					$clientLogger->addError($e->getMessage());
				}
			}
			if (isset($server)) {
				try {
					$this->shutdownServer($server);
				}
				catch (\Exception $e_) {
					$serverLogger->addError($e->getMessage());
				}
			}
			throw $e;
		}
	}

	/**
	 * @covers \RemoteObjects\Client::getRemoteObject
	 */
	public function testChaining()
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->addDebug('--- InvocationTestCase::testChaining() ---');

			$server = $this->spawnServer(
				array(
					 'echo' => new EchoObject()
				),
				$serverLogger
			);
			$client = $this->spawnClient($clientLogger);

			/** @var RemoteObject $remote */
			/** @var EchoInterface $echo */
			$remote = $client->castAsRemoteObject();

			$this->assertTrue(
				$remote instanceof RemoteObject
			);
			$this->assertAttributeEquals(
				null,
				'path',
				$remote
			);

			$echo = $remote->echo;

			$this->assertTrue(
				$echo instanceof RemoteObject
			);
			$this->assertAttributeEquals(
				'echo',
				'path',
				$echo
			);
			$this->assertEquals(
				'Hello World!',
				$echo->reply('Hello World!')
			);

			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch (\Exception $e) {
			if (isset($client)) {
				try {
					$this->shutdownClient($client);
				}
				catch (\Exception $e_) {
					$clientLogger->addError($e->getMessage());
				}
			}
			if (isset($server)) {
				try {
					$this->shutdownServer($server);
				}
				catch (\Exception $e_) {
					$serverLogger->addError($e->getMessage());
				}
			}
			throw $e;
		}
	}
}
