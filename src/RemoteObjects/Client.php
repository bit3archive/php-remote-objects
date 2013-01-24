<?php

namespace RemoteObjects;

use RemoteObjects\Proxy\RemoteObjectProxy;
use RemoteObjects\Proxy\RemoteObjectProxyGenerator;

class Client
{
	/**
	 * The logger facility.
	 *
	 * @var \Monolog\Logger
	 */
	protected $logger;

	/**
	 * The transport way.
	 *
	 * @var \RemoteObjects\Transport\Client
	 */
	protected $transport;

	/**
	 * The data encoder.
	 *
	 * @var \RemoteObjects\Encode\Encoder
	 */
	protected $encoder;

	/**
	 * @param string                         $endpoint
	 * @param RemoteObjects\Transport\Client $transport
	 */
	function __construct(\RemoteObjects\Transport\Client $transport, \RemoteObjects\Encode\Encoder $encoder)
	{
		$this->transport = $transport;
		$this->encoder   = $encoder;
	}

	/**
	 * @param \Monolog\Logger $logger
	 */
	public function setLogger($logger)
	{
		$this->logger = $logger;
		return $this;
	}

	/**
	 * @return \Monolog\Logger
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * @param \RemoteObjects\Transport\Client $transport
	 */
	public function setTransport($transport)
	{
		$this->transport = $transport;
		return $this;
	}

	/**
	 * @return \RemoteObjects\Transport\Client
	 */
	public function getTransport()
	{
		return $this->transport;
	}

	/**
	 * @param \RemoteObjects\Encode\Encoder $encoder
	 */
	public function setEncoder($encoder)
	{
		$this->encoder = $encoder;
		return $this;
	}

	/**
	 * @return \RemoteObjects\Encode\Encoder
	 */
	public function getEncoder()
	{
		return $this->encoder;
	}

	/**
	 * Cast the client directly into an object object.
	 *
	 * @param string|null $interface
	 *
	 * @return RemoteObject
	 */
	public function castAsRemoteObject($interface = null)
	{
		return $this->getRemoteObject(null, $interface);
	}

	/**
	 * Get a named remote object proxy.
	 *
	 * @param string $key
	 * @param string|null $interface
	 *
	 * @return RemoteObject
	 */
	public function getRemoteObject($key, $interface = null)
	{
		if ($interface) {
			return RemoteObjectProxyGenerator::generate($this, $interface, $key);
		}
		else {
			return new RemoteObjectProxy($this, $key);
		}
	}

	/**
	 * Invoke a remote method.
	 *
	 * @param string $method The method name.
	 * @param mixed  $_      List of method parameters.
	 *
	 * @return mixed
	 */
	public function invoke($method, $_ = null)
	{
		$params = func_get_args();
		array_shift($params);

		return $this->invokeArgs($method, $params);
	}

	/**
	 * Invoke a remote method.
	 *
	 * @param string $method The method name.
	 * @param array  $params List of method parameters.
	 *
	 * @return mixed
	 */
	public function invokeArgs($method, array $params)
	{
		if (
			$this->logger !== null &&
			$this->logger->isHandling(\Monolog\Logger::DEBUG)
		) {
			$methodSynopsis = $method . '(';
			foreach (array_values($params) as $index => $param) {
				if ($index > 0) {
					$methodSynopsis .= ', ';
				}
				$methodSynopsis .= var_export($param, true);
			}
			$methodSynopsis .= ')';

			$this->logger->addDebug(
				'Invoke remote object method ' . $methodSynopsis,
				array(
					 'transport' => $this->transport,
					 'method'    => $method,
					 'params'    => $params
				)
			);
		}

		$request = $this->encoder->encodeMethod($method, $params);

		$response = $this->transport->request($request);

		$result = $this->encoder->decodeResult($response);

		if (
			$this->logger !== null &&
			$this->logger->isHandling(\Monolog\Logger::DEBUG)
		) {
			$this->logger->addDebug(
				'Receive methods ' . $methodSynopsis . ' result.',
				array(
					 'transport' => $this->transport,
					 'method'    => $method,
					 'params'    => $params
				)
			);
		}

		return $result;
	}
}
