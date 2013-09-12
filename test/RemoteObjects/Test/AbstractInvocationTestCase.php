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
use RemoteObjects\Encode\AesEncoder;
use RemoteObjects\Encode\Encoder;
use RemoteObjects\Encode\JsonRpc20Encoder;
use RemoteObjects\Encode\RsaEncoder;
use RemoteObjects\Server;
use RemoteObjects\Client;
use RemoteObjects\RemoteObject;

/**
 * Class AbstractInvocationTestCase
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Test
 * @api
 */
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
	protected abstract function spawnServer($target, $logger, Encoder $encoder);

	/**
	 * @return Server
	 */
	protected abstract function shutdownServer($server);

	/**
	 * @return Client
	 */
	protected abstract function spawnClient($logger, Encoder $encoder);

	/**
	 * @return Client
	 */
	protected abstract function shutdownClient($client);

	public function providerEncoderChains()
	{
		$aesPsk = md5(uniqid(mt_rand()));

		$rsa = new \Crypt_RSA();
		$serverKey = $rsa->createKey();
		$clientKey = $rsa->createKey();

		return array(
			array(
				new JsonRpc20Encoder(),
				new JsonRpc20Encoder(),
			),
			array(
				new AesEncoder(
					new JsonRpc20Encoder(),
					$aesPsk
				),
				new AesEncoder(
					new JsonRpc20Encoder(),
					$aesPsk
				),
			),
			array(
				new RsaEncoder(
					new JsonRpc20Encoder(),
					$clientKey['publickey'],
					$serverKey['privatekey']
				),
				new RsaEncoder(
					new JsonRpc20Encoder(),
					$serverKey['publickey'],
					$clientKey['privatekey']
				)
			)
		);
	}
	
	public function providerRemoteObjectTypes()
	{
		$encoderChain = $this->providerEncoderChains();

		$data = array();

		foreach (
			array(
				null,
				'RemoteObjects\Test\EchoInterface',
				'RemoteObjects\Test\EchoObject'
			) as $type
		) {
			foreach ($encoderChain as $encoders) {
				$data[] = array_merge(
					array($type),
					$encoders
				);
			}
		}

		return $data;
	}

	/**
	 * @dataProvider providerEncoderChains
	 * @covers \RemoteObjects\Client::invoke
	 * @covers \RemoteObjects\Server::handle
	 */
	public function testInvoke(Encoder $serverEncoder, Encoder $clientEncoder)
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->debug(
				sprintf(
					'--- InvocationTestCase::testInvoke(%s, %s) ---',
					get_class($serverEncoder),
					get_class($clientEncoder)
				)
			);

			$server = $this->spawnServer(new EchoObject(), $serverLogger, $serverEncoder);
			$client = $this->spawnClient($clientLogger, $clientEncoder);

			$result = $client->invoke('reply', 'Hello World!');
			$this->assertEquals('Hello World!', $result);

			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch (\Exception $e) {
			$logger->error($e->getMessage());

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
	 * @dataProvider providerEncoderChains
	 * @covers \RemoteObjects\Client::invoke
	 * @covers \RemoteObjects\Server::handle
	 */
	public function testInvalidMethod(Encoder $serverEncoder, Encoder $clientEncoder)
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->debug(
				sprintf(
					'--- InvocationTestCase::testInvalidMethod(%s, %s) ---',
					get_class($serverEncoder),
					get_class($clientEncoder)
				)
			);

			$server = $this->spawnServer(new EchoObject(), $serverLogger, $serverEncoder);
			$this->setExpectedException('Exception', 'Method not found', -32601);

			$client = $this->spawnClient($clientLogger, $clientEncoder);

			$client->invoke('unknownMethod');
			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch (\Exception $e) {
			$logger->error($e->getMessage());

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
	 * @dataProvider providerRemoteObjectTypes
	 * @covers \RemoteObjects\Client::castAsRemoteObject
	 */
	public function testClientCast($remoteObjectType, Encoder $serverEncoder, Encoder $clientEncoder)
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->debug(
				sprintf(
					'--- InvocationTestCase::testClientCast(%s, %s, %s) ---',
					$remoteObjectType ? $remoteObjectType : 'null',
					get_class($serverEncoder),
					get_class($clientEncoder)
				)
			);

			$server = $this->spawnServer(new EchoObject(), $serverLogger, $serverEncoder);
			$client = $this->spawnClient($clientLogger, $clientEncoder);

			/** @var EchoInterface $echo */
			$echo = $client->castAsRemoteObject($remoteObjectType);

			$this->assertTrue(
				$echo instanceof RemoteObject
			);
			if ($remoteObjectType !== null) {
				$this->assertTrue(
					is_a($echo, $remoteObjectType)
				);
			}
			$this->assertEquals(
				'Hello World!',
				$echo->reply('Hello World!')
			);

			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch (\Exception $e) {
			$logger->error($e->getMessage());

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
	 * @dataProvider providerRemoteObjectTypes
	 * @covers \RemoteObjects\Client::getRemoteObject
	 */
	public function testNamedRemote($remoteObjectType, Encoder $serverEncoder, Encoder $clientEncoder)
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->debug(
				sprintf(
					'--- InvocationTestCase::testNamedRemote(%s, %s, %s) ---',
					$remoteObjectType ? $remoteObjectType : 'null',
					get_class($serverEncoder),
					get_class($clientEncoder)
				)
			);

			$server = $this->spawnServer(
				array(
					 'echo' => new EchoObject()
				),
				$serverLogger, $serverEncoder
			);
			$client = $this->spawnClient($clientLogger, $clientEncoder);

			/** @var EchoInterface $echo */
			$echo = $client->getRemoteObject('echo', $remoteObjectType);

			$this->assertTrue(
				$echo instanceof RemoteObject
			);
			if ($remoteObjectType !== null) {
				$this->assertTrue(
					is_a($echo, $remoteObjectType)
				);
				$this->assertAttributeEquals(
					'echo',
					'___path',
					$echo
				);
			}
			else {
				$this->assertAttributeEquals(
					'echo',
					'path',
					$echo
				);
			}
			$this->assertEquals(
				'Hello World!',
				$echo->reply('Hello World!')
			);

			$this->shutdownClient($client);
			$this->shutdownServer($server);
		}
		catch (\Exception $e) {
			$logger->error($e->getMessage());

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
	 * @dataProvider providerEncoderChains
	 * @covers \RemoteObjects\Client::getRemoteObject
	 */
	public function testChaining(Encoder $serverEncoder, Encoder $clientEncoder)
	{
		$logger = $this->getLogger('phpunit');
		$serverLogger = $this->getLogger('server');
		$clientLogger = $this->getLogger('client');

		try {
			$logger->debug(
				sprintf(
					'--- InvocationTestCase::testChaining(%s, %s) ---',
					get_class($serverEncoder),
					get_class($clientEncoder)
				)
			);

			$server = $this->spawnServer(
				array(
					 'echo' => new EchoObject()
				),
				$serverLogger, $serverEncoder
			);
			$client = $this->spawnClient($clientLogger, $clientEncoder);

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
			$logger->error($e->getMessage());

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
