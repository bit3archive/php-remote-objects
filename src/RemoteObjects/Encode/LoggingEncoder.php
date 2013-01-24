<?php

namespace RemoteObjects\Encode;

abstract class LoggingEncoder implements Encoder
{
	/**
	 * The logger facility.
	 *
	 * @var \Monolog\Logger
	 */
	protected $logger;

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
}
