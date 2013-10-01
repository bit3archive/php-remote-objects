<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class LoggingEncoder
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Encode
 * @api
 */
abstract class LoggerAware implements LoggerAwareInterface
{
	/**
	 * The logger facility.
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * {@inheritdoc}
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
		return $this;
	}

	/**
	 * Return the logger facility.
	 *
	 * @return LoggerInterface|null
	 */
	public function getLogger()
	{
		return $this->logger;
	}
}
