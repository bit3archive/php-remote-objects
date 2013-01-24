<?php

namespace RemoteObjects\Encode;

class JsonRpc20Encoder implements Encoder
{
	/**
	 * The logger facility.
	 *
	 * @var \Monolog\Logger
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
	 * @param \Monolog\Logger $logger
	 */
	public function setLogger($logger)
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
		$json->trace   = $exception->getTraceAsString();
		$json->data    = $this->encodeExceptionObject($exception->getPrevious());
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

		if ($json === null) {
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

		if (substr($json->method, 0, 4) == 'rpc.') {
			return array(null, null);
		}

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
