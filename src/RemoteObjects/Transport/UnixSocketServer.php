<?php

namespace RemoteObjects\Transport;

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
	public function respond($json)
	{
		$socket = $this->getSocket();

		$this->socketSend($socket, $json);
	}
}
