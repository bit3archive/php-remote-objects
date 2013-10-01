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

use RemoteObjects\Internal\EncodedException;
use RemoteObjects\Internal\EncodedMethodResultInterface;
use RemoteObjects\Internal\MethodInterface;

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
	public function encodeMethod(MethodInterface $method);

	/**
	 * Encode an exception.
	 *
	 * @param \Exception $exception
	 *
	 * @return EncodedException
	 */
	public function encodeException(MethodInterface $method, \Exception $exception);

	/**
	 * Encode a result from the method call.
	 *
	 * @param MethodResultInterface $result
	 *
	 * @return EncodedMethodResultInterface
	 */
	public function encodeResult(MethodResultInterface $result);
}
