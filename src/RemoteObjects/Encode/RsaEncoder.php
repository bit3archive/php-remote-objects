<?php

namespace RemoteObjects\Encode;

class RsaEncoder implements Encoder
{
	/**
	 * The logger facility.
	 *
	 * @var \Monolog\Logger
	 */
	protected $logger;

	/**
	 * @var Encoder
	 */
	protected $encoder;

	/**
	 * @var string
	 */
	protected $remotePublicKey;

	/**
	 * @var string
	 */
	protected $localPrivateKey;

	/**
	 * The crypt component.
	 *
	 * @var \Crypt_RSA
	 */
	protected $rsa;

	/**
	 * @param Encoder $encoder
	 * @param string $remotePublicKey
	 * @param string $localPrivateKey
	 */
	function __construct(Encoder $encode, $remotePublicKey, $localPrivateKey)
	{
		$this->remotePublicKey = $remotePublicKey;
		$this->localPrivateKey = $localPrivateKey;
	}

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

	/**
	 * @param string $remotePublicKey
	 */
	public function setRemotePublicKey($remotePublicKey)
	{
		$this->remotePublicKey = $remotePublicKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRemotePublicKey()
	{
		return $this->remotePublicKey;
	}

	/**
	 * @param string $localPrivateKey
	 */
	public function setLocalPrivateKey($localPrivateKey)
	{
		$this->localPrivateKey = $localPrivateKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLocalPrivateKey()
	{
		return $this->localPrivateKey;
	}

	/**
	 * Get the crypt rsa component.
	 *
	 * @return \Crypt_RSA
	 */
	protected function getCryptRsa()
	{
		if ($this->rsa === null) {
			$this->rsa = new \Crypt_RSA();
		}

		return $this->rsa;
	}

	public function encrypt($string)
	{
		$rsa = $this->getCryptRsa();
		$rsa->loadKey($this->remotePublicKey);

		$crypt = $rsa->encrypt($string);

		return $crypt;
	}

	public function decrypt($crypt)
	{
		$rsa = $this->getCryptRsa();
		$rsa->loadKey($this->localPrivateKey);

		$string = $rsa->decrypt($crypt);

		return $string;
	}

	/**
	 * Encode a method call.
	 *
	 * @param $mixed
	 *
	 * @return string
	 */
	public function encodeMethod($method, $params)
	{
		$string = $this->encoder->encodeMethod($method, $params);

		return $this->encrypt($string);
	}

	/**
	 * Encode an exception.
	 *
	 * @param \Exception $exception
	 *
	 * @return string
	 */
	public function encodeException(\Exception $exception)
	{
		$string = $this->encoder->encodeException($exception);

		return $this->encrypt($string);
	}

	/**
	 * Encode a result from the method call.
	 *
	 * @param mixed $result
	 *
	 * @return string
	 */
	public function encodeResult($result)
	{
		$string = $this->encoder->encodeResult($result);

		return $this->encrypt($string);
	}

	/**
	 * Decode an encoded method.
	 *
	 * @param $string
	 *
	 * @return array An array with 2 elements: [<method name>, <method params>]
	 *
	 * @throws Throw an exception, if $string is an error or contains an error.
	 */
	public function decodeMethod($crypt)
	{
		$string = $this->decrypt($crypt);

		return $this->encoder->decodeMethod($string);
	}

	/**
	 * Decode an encoded result.
	 *
	 * @param $string
	 *
	 * @return mixed
	 *
	 * @throws Throw an exception, if $string is an error or contains an error.
	 */
	public function decodeResult($crypt)
	{
		$string = $this->decrypt($crypt);

		return $this->encoder->decodeResult($string);
	}
}
