<?php

namespace RemoteObjects\Transport;

interface Server
{
	/**
	 * Receive the json request.
	 *
	 * @return stdClass
	 */
	public function receive();

	/**
	 * Send response.
	 *
	 * @param mixed      $result
	 * @param \Exception $error
	 */
	public function respond($json);
}
