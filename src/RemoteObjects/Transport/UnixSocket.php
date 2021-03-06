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
 * Class UnixSocket
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Transport
 * @api
 */
abstract class UnixSocket
{
	/**
	 * The logger facility.
	 *
	 * @var \Monolog\Logger
	 */
	protected $logger;

	/**
	 * @var string
	 */
	protected $socketPath;

	/**
	 * @var bool
	 */
	protected $targetSocketPath;

	/**
	 * @var resource
	 */
	protected $socket;

	/**
	 * @param string $clientSocketPath
	 */
	protected function __construct($socketPath, $targetSocketPath)
	{
		$this->socketPath       = (string) $socketPath;
		$this->targetSocketPath = $targetSocketPath;
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

	protected function getSocket()
	{
		if ($this->socket === null) {
			if (
				$this->logger !== null &&
				$this->logger->isHandling(Logger::DEBUG)
			) {
				$this->logger->addDebug(
					'Create new socket',
					array(
						 'socketPath' => $this->socketPath
					)
				);
			}

			// create curl instance
			$this->socket = socket_create(
				AF_UNIX,
				SOCK_DGRAM,
				0
			);

			socket_bind(
				$this->socket,
				$this->socketPath
			);
		}

		return $this->socket;
	}

	public function close()
	{
		if ($this->socket !== null) {
			if (
				$this->logger !== null &&
				$this->logger->isHandling(Logger::DEBUG)
			) {
				$this->logger->addDebug(
					'Close socket',
					array(
						 'socketPath' => $this->socketPath
					)
				);
			}

			socket_close($this->socket);
			$this->socket = null;
			unlink($this->socketPath);
		}
	}

	protected function socketSend($socket, $message)
	{
		if (
			$this->logger !== null &&
			$this->logger->isHandling(Logger::DEBUG)
		) {
			$this->logger->addDebug(
				'Send message to socket',
				array(
					 'socketPath'       => $this->socketPath,
					 'targetSocketPath' => $this->targetSocketPath,
					 'message'          => $message
				)
			);
		}

		$sent = socket_sendto($socket, $message, strlen($message), 0, $this->targetSocketPath);

		if ($sent == -1) {
			if (
				$this->logger !== null &&
				$this->logger->isHandling(Logger::ERROR)
			) {
				$this->logger->addError(
					'Could not block socket',
					array(
						 'socketPath' => $this->socketPath,
						 'error'      => socket_last_error($socket)
					)
				);
			}

			throw new \Exception('Could not block socket ' . $this->socketPath . ': ' . socket_last_error($socket));
		}
	}

	protected function socketReceive($socket)
	{
		if (
			$this->logger !== null &&
			$this->logger->isHandling(Logger::DEBUG)
		) {
			$this->logger->addDebug(
				'Wait for message on socket',
				array(
					 'socketPath' => $this->socketPath
				)
			);
		}

		if (!socket_set_block($socket)) {
			if (
				$this->logger !== null &&
				$this->logger->isHandling(Logger::ERROR)
			) {
				$this->logger->addError(
					'Could not block socket',
					array(
						 'socketPath' => $this->socketPath,
						 'error'      => socket_last_error($socket)
					)
				);
			}

			throw new \Exception('Could not block socket ' . $this->socketPath . ': ' . socket_last_error($socket));
		}

		$buffer = '';
		$from   = '';

		$received = socket_recvfrom($this->socket, $buffer, 65536, 0, $from);

		if (-1 == $received) {
			if (
				$this->logger !== null &&
				$this->logger->isHandling(Logger::ERROR)
			) {
				$this->logger->addError(
					'Could not receive data from socket',
					array(
						 'socketPath' => $this->socketPath,
						 'error'      => socket_last_error($socket)
					)
				);
			}

			throw new \Exception(
				'Could not receive data from the socket ' . $this->socketPath . ': ' . socket_last_error($socket)
			);
		}

		if (!socket_set_nonblock($socket)) {
			if (
				$this->logger !== null &&
				$this->logger->isHandling(Logger::ERROR)
			) {
				$this->logger->addError(
					'Could not unblock socket',
					array(
						 'socketPath' => $this->socketPath,
						 'error'      => socket_last_error($socket)
					)
				);
			}

			throw new \Exception('Could not unblock socket ' . $this->socketPath . ': ' . socket_last_error($socket));
		}

		$this->targetSocketPath = $from;

		if (
			$this->logger !== null &&
			$this->logger->isHandling(Logger::DEBUG)
		) {
			$this->logger->addDebug(
				'Received message on socket',
				array(
					 'socketPath'       => $this->socketPath,
					 'targetSocketPath' => $this->targetSocketPath,
					 'message'          => $buffer
				)
			);
		}

		return $buffer;
	}
}
