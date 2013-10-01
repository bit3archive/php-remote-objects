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

use RemoteObjects\Internal\EncodedMethodInterface;
use RemoteObjects\Internal\EncodedMethodResultInterface;
use RemoteObjects\MethodInterface;
use RemoteObjects\MethodResultInterface;

/**
 * Class Decoder
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Encode
 * @api
 */
interface Decoder
{
	/**
	 * Decode an encoded method.
	 *
	 * @param EncodedMethodInterface $encodedMethod
	 *
	 * @return MethodInterface
	 *
	 * @throws Throw an exception, if $encodedMethod is an error or contains an error.
	 */
	public function decodeMethod(EncodedMethodInterface $encodedMethod);

	/**
	 * Decode an encoded result.
	 *
	 * @param EncodedMethodResultInterface $encodedResult
	 *
	 * @return MethodResultInterface
	 *
	 * @throws Throw an exception, if $encodedResult is an error or contains an error.
	 */
	public function decodeResult(EncodedMethodResultInterface $encodedResult);
}
