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

/**
 * Class UnixSocketServer
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Transport
 * @api
 */
class UnixSocketServer extends UnixSocket implements Server
{
	/**
	 * @param string $clientSocketPath
	 */
	function __construct($serverSocketPath)
	{
		parent::__construct($serverSocketPath, false);
	}

	/**
	 * Receive and deserialize the json request.
	 *
	 * @return stdClass
	 */
	public function receive()
	{
		$socket = $this->getSocket();

		$request = $this->socketReceive($socket);

		return $request;
	}

	/**
	 * Send response.
	 *
	 * @param mixed      $result
	 * @param \Exception $error
	 */
	public function respond($response)
	{
		$socket = $this->getSocket();

		$this->socketSend($socket, $response);
	}
}
