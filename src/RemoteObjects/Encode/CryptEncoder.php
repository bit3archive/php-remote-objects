<?php

namespace RemoteObjects\Encode;

abstract class CryptEncoder extends LoggingEncoder
{
	/**
	 * @var Encoder
	 */
	protected $encoder;

	/**
	 * @var bool
	 */
	protected $plainExceptions;

	/**
	 * @param Encoder $encoder
	 * @param bool    $plainExceptions If encryption fails, return a plain unencrypted exception.
	 *                                 This is only useful on servers and for debugging.
	 */
	function __construct(Encoder $encoder, $plainExceptions = false)
	{
		$this->encoder         = $encoder;
		$this->plainExceptions = $plainExceptions;
	}

	public abstract function encrypt($string);

	public abstract function decrypt($crypt);

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

		try {
			return $this->encrypt($string);
		}
		catch (\Exception $e) {
			if ($this->plainExceptions) {
				return $string;
			}
			throw $e;
		}
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
