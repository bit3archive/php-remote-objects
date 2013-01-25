<?php

namespace RemoteObjects;

class Server
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
	 * @var \RemoteObjects\Transport\Server
	 */
	protected $transport;

	/**
	 * The data encoder.
	 *
	 * @var \RemoteObjects\Encode\Encoder
	 */
	protected $encoder;

	/**
	 * The target to invoke methods on.
	 *
	 * @var mixed
	 */
	protected $target;

	/**
	 * @param mixed            $target
	 * @param Transport\Server $transport
	 */
	public function __construct(
		\RemoteObjects\Transport\Server $transport,
		\RemoteObjects\Encode\Encoder $encoder,
		$target
	) {
		$this->transport = $transport;
		$this->encoder   = $encoder;
		$this->target    = $target;
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
	 * @param \RemoteObjects\Transport\Server $transport
	 */
	public function setTransport(\RemoteObjects\Transport\Server $transport)
	{
		$this->transport = $transport;
		return $this;
	}

	/**
	 * @return \RemoteObjects\Transport\Server
	 */
	public function getTransport()
	{
		return $this->transport;
	}

	/**
	 * @param \RemoteObjects\Encode\Encoder $encoder
	 */
	public function setEncoder(\RemoteObjects\Encode\Encoder $encoder)
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
	 * @param mixed $target
	 */
	public function setTarget($target)
	{
		$this->target = $target;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * Handle a remote invokation request.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		try {
			$request = $this->transport->receive();

			list($method, $params) = $this->encoder->decodeMethod($request);

			if (!$method) {
				$result = null;
			}
			else {
				if (
					$this->logger !== null &&
					$this->logger->isHandling(\Monolog\Logger::DEBUG)
				) {
					$this->logger->addDebug(
						'Receive remote method invocation',
						array(
							 'method' => $method,
							 'params' => $params
						)
					);
				}

				$result = $this->invoke(
					$this->target,
					$method,
					$params
				);
			}

			$response = $this->encoder->encodeResult($result);

			$this->transport->respond($response);
		}
		catch (\Exception $e) {
			$response = $this->encoder->encodeException($e);

			$this->transport->respond($response);
		}
	}

	/**
	 * @param mixed  $targetObject
	 * @param string $methodName
	 * @param array  $methodParams
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	protected function invoke($targetObject, $methodName, array $methodParams)
	{
		if (strpos($methodName, '.') !== false) {
			list($propertyName, $methodName) = explode('.', $methodName, 2);

			// access native array item
			if (is_array($targetObject)) {
				if (array_key_exists($propertyName, $targetObject)) {
					return $this->invoke(
						$targetObject[$propertyName],
						$methodName,
						$methodParams
					);
				}
			}

			// access array object
			else if ($targetObject instanceof \ArrayAccess) {
				if ($targetObject->offsetExists($propertyName)) {
					return $this->invoke(
						$targetObject->offsetGet($propertyName),
						$methodName,
						$methodParams
					);
				}
			}

			// access object property
			else if (is_object($targetObject)) {
				if (get_class($targetObject) == 'stdClass') {
					if (isset($targetObject->$propertyName)) {
						return $this->invoke(
							$targetObject->$propertyName,
							$methodName,
							$methodParams
						);
					}
				}
				else {
					$class = new \ReflectionClass($targetObject);

					if ($class->hasProperty($propertyName)) {
						$property = $class->getProperty($propertyName);
						if ($property->isPublic()) {
							return $this->invoke(
								$property->getValue($targetObject),
								$methodName,
								$methodParams
							);
						}
					}

					$getterName = 'get' . implode('', array_map('ucfirst', explode('_', $property)));
					if ($class->hasMethod($getterName)) {
						$getter = $class->getMethod($getterName);
						if ($getter->isPublic()) {
							return $this->invoke(
								$getter->invoke($targetObject),
								$methodName,
								$methodParams
							);
						}
					}
				}
			}
		}
		else if (is_object($targetObject)) {
			$class = new \ReflectionClass($targetObject);

			if ($class->hasMethod($methodName)) {
				$method = $class->getMethod($methodName);
				if ($method->isPublic()) {
					if (
						$this->logger !== null &&
						$this->logger->isHandling(\Monolog\Logger::DEBUG)
					) {
						$this->logger->addDebug(
							'Invoke method from remote',
							array(
								 'class' => get_class($targetObject),
								 'method' => $methodName,
								 'params' => $methodParams
							)
						);
					}

					return $method->invokeArgs($targetObject, $methodParams);
				}
			}
		}

		if (
			$this->logger !== null &&
			$this->logger->isHandling(\Monolog\Logger::DEBUG)
		) {
			$this->logger->addError(
				'Could not invoke method, because method not exists or not accessible',
				array(
					 'class' => get_class($targetObject),
					 'method' => $methodName,
					 'params' => $methodParams
				)
			);
		}

		throw new \Exception('Method not found', -32601);
	}
}
