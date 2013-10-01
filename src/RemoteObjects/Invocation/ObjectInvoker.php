<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Invocation;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RemoteObjects\Transport\Client;

/**
 * Class Server
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
class ObjectInvoker implements InvokerInterface, LoggerAwareInterface
{
	/**
	 * @var Client
	 */
	protected $transport;
	
	/**
	 * The logger facility.
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @param mixed $targetObject
	 */
	function __construct($targetObject)
	{
		$this->transport = $targetObject;
	}

	/**
	 * @param mixed $targetObject
	 */
	public function setTransport($targetObject)
	{
		$this->transport = $targetObject;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTransport()
	{
		return $this->transport;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
		return $this;
	}

	/**
	 * @return LoggerInterface
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * {@inheritdoc}
	 */
	public function invoke($methodName, array $methodParams)
	{
		// TODO
	}
}
