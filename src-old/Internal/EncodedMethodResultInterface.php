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
 * Class EncodedMethodResultInterface
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
interface EncodedMethodResultInterface
{
	/**
	 * Return the method result.
	 *
	 * @return MethodResultInterface
	 */
	public function getMethodResult();

	/**
	 * Return the encoded result.
	 *
	 * @return string
	 */
	public function getEncodedResult();
}
