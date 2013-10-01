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
 * Class MethodExceptionInterface
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
interface MethodExceptionInterface
{
	/**
	 * Return the called method.
	 *
	 * @return MethodInterface
	 */
	public function getMethod();

	/**
	 * Return the result from the called method.
	 *
	 * @return \Exception
	 */
	public function getException();
}
