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
 * Class UnixSocketClient
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Transport
 * @api
 */
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
