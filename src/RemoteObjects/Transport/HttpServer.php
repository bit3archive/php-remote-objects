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

use Monolog\Logger;

/**
 * Class HttpServer
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Transport
 * @api
 */
class HttpServer implements Server
{
	/**
	 * The logger facility.
	 *
	 * @var \Monolog\Logger
	 */
	protected $logger;

	protected $contentType;

	function __construct($contentType = 'application/octet-stream')
	{
		$this->contentType = $contentType;
	}

	/**
	 * @param \Monolog\Logger $logger
	 */
	public function setLogger(Logger $logger)
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
	 * Receive the serialized json request.
	 *
	 * @return stdClass
	 */
	public function receive()
	{
		$input = file_get_contents('php://input');

		if (
			$this->logger !== null &&
			$this->logger->isHandling(Logger::DEBUG)
		) {
			$this->logger->addDebug(
				'Receive request',
				array(
					 'request' => ctype_print($input) ? $input : 'base64:' . base64_encode($input)
				)
			);
		}

		return $input;
	}

	/**
	 * Send response.
	 *
	 * @param mixed      $result
	 * @param \Exception $error
	 */
	public function respond($response)
	{
		if (
			$this->logger !== null &&
			$this->logger->isHandling(Logger::DEBUG)
		) {
			$this->logger->addDebug(
				'Send response',
				array(
					 'content-type' => $this->contentType,
					 'response'     => ctype_print($response) ? $response : 'base64:' . base64_encode($response)
				)
			);
		}

		ob_start();
		while (ob_end_clean()) {
		}
		header('Content-Type: ' . $this->contentType);
		echo $response;
	}
}
