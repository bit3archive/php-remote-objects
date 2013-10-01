<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Transport;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SimpleHttpServer
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Transport
 * @api
 */
class HttpServer implements TransportServer, LoggerAwareInterface
{
	/**
	 * The logger facility.
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @param LoggerInterface $logger
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
	 * Receive the serialized json request.
	 *
	 * @return Request
	 */
	public function receive()
	{
		$request = Request::createFromGlobals();

		if ($this->logger !== null) {
			$this->logger->debug(
				'Receive request',
				array(
					'request' => $request
				)
			);
		}

		return $request;
	}

	/**
	 * Send response.
	 *
	 * @param mixed      $result
	 * @param \Exception $error
	 */
	public function respond(Response $response)
	{
		if ($this->logger !== null) {
			$this->logger->debug(
				'Send response',
				array(
					'response' => $response
				)
			);
		}

		$response->send();
	}
}
