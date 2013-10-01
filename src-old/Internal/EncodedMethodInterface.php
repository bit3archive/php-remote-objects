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
 * Class EncodedMethodInterface
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
interface EncodedMethodInterface
{
	/**
	 * Return the method, if it is available.
	 *
	 * @return Method|null
	 */
	public function getMethod();

	/**
	 * Return the encoded method.
	 *
	 * @return mixed
	 */
	public function getEncodedMethod();
}
