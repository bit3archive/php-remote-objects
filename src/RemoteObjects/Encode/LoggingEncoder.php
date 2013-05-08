<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Encode;

/**
 * Class LoggingEncoder
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Encode
 * @api
 */
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
