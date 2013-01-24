<?php

namespace RemoteObjects\Transport;

class HttpServer implements Server
{
	/**
	 * Receive the serialized json request.
	 *
	 * @return stdClass
	 */
	public function receive()
	{
		$input = file_get_contents('php://input');

		return $input;
	}

	/**
	 * Send response.
	 *
	 * @param mixed      $result
	 * @param \Exception $error
	 */
	public function respond($json)
	{
		while(ob_end_clean());
		header('Content-Type: application/octet-stream');
		echo $json;
	}
}
