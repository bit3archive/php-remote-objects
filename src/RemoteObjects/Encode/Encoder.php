<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Encode;

/**
 * Class Encoder
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Encode
 * @api
 */
interface Encoder
{
	/**
	 * Encode a method call.
	 *
	 * @param $mixed
	 *
	 * @return string
	 */
	public function encodeMethod($method, $params);

	/**
	 * Encode an exception.
	 *
	 * @param \Exception $exception
	 *
	 * @return string
	 */
	public function encodeException(\Exception $exception);

	/**
	 * Encode a result from the method call.
	 *
	 * @param mixed $result
	 *
	 * @return string
	 */
	public function encodeResult($result);

	/**
	 * Decode an encoded method.
	 *
	 * @param $string
	 *
	 * @return array An array with 2 elements: [<method name>, <method params>]
	 *
	 * @throws Throw an exception, if $string is an error or contains an error.
	 */
	public function decodeMethod($string);

	/**
	 * Decode an encoded result.
	 *
	 * @param $string
	 *
	 * @return mixed
	 *
	 * @throws Throw an exception, if $string is an error or contains an error.
	 */
	public function decodeResult($string);
}
