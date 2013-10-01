<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Internal;

/**
 * Class EncodedExceptionInterface
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
interface EncodedExceptionInterface
{
	/**
	 * Return the method exception.
	 *
	 * @return MethodExceptionInterface
	 */
	public function getMethodException();

	/**
	 * Return the encoded method exception.
	 *
	 * @return mixed
	 */
	public function getEncodedMethodException();
}
