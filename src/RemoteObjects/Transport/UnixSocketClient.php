<?php

namespace RemoteObjects\Transport;

class UnixSocketClient extends UnixSocket implements Client
{
	/**
	 * @param string $clientSocketPath
	 */
	function __construct($clientSocketPath, $serverSocketPath)
	{
		parent::__construct($clientSocketPath, $serverSocketPath);
	}

	public function request($json)
	{
		$socket = $this->getSocket();

		$this->socketSend($socket, $json);

		$response = $this->socketReceive($socket);

		return $response;
	}
}
