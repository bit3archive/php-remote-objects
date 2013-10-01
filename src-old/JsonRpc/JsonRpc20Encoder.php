<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\JsonRpc;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class JsonRpc20Encoder
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Encode
 * @api
 */
class JsonRpc20Encoder implements Encoder, LoggerAwareInterface
{
	/**
	 * The logger facility.
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * The id of the last request.
	 *
	 * @var int
	 */
	protected $lastRequestId = 0;

	/**
	 * The last id from JsonRpc20Encoder::decode()
	 *
	 * @var mixed
	 */
	protected $lastDecodeId = null;

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

	public function encodeMethod($method, $params)
	{
		$json          = new \stdClass();
		$json->jsonrpc = '2.0';
		$json->method  = (string) $method;
		$json->params  = $params;
		$json->id      = ++$this->lastRequestId;
		return json_encode($json);
	}

	protected function encodeExceptionObject($exception)
	{
		if (!$exception) {
			return null;
		}

		$json          = new \stdClass();
		$json->code    = $exception->getCode();
		$json->message = $exception->getMessage();
		#$json->trace   = $exception->getTraceAsString();
		#$json->data    = $this->encodeExceptionObject($exception->getPrevious());
		return $json;
	}

	public function encodeException(\Exception $exception)
	{
		$response          = new \stdClass();
		$response->jsonrpc = '2.0';
		$response->result  = null;
		$response->error   = $this->encodeExceptionObject($exception);
		$response->id      = $this->lastDecodeId;
		return json_encode($response);
	}

	public function encodeResult($result)
	{
		$response          = new \stdClass();
		$response->jsonrpc = '2.0';
		$response->result  = $result;
		$response->error   = null;
		$response->id      = $this->lastDecodeId;
		return json_encode($response);
	}

	protected function decode($string)
	{
		$json = json_decode($string);

		if (!is_object($json)) {
			throw new \Exception(
				'Parse error',
				-32700,
				new \Exception(
					'Decode json failed',
					json_last_error()
				)
			);
		}

		if ($json->jsonrpc != '2.0') {
			throw new \Exception('Invalid Request', -32600);
		}

		return $json;
	}

	public function decodeMethod($string)
	{
		$json = $this->decode($string);

		if (
			!isset($json->jsonrpc) ||
			!isset($json->method)
		) {
			throw new \Exception('Invalid Request', -32600);
		}

		$this->lastDecodeId = $json->id;

		/*
		if (substr($json->method, 0, 4) == 'rpc.') {
			return array(null, null);
		}
		*/

		return array(
			$json->method,
			isset($json->params) ? $json->params : null
		);
	}

	public function decodeResult($string)
	{
		$json = $this->decode($string);

		if (isset($json->error)) {
			throw new \Exception(
				$json->error->message,
				$json->error->code
			);
		}

		return $json->result;
	}

}
