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
 * Class MethodInterface
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
interface MethodInterface
{
	/**
	 * Return the method name.
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * An ordered array with the method parameters.
	 *
	 * @return array
	 */
	public function getParameters();
}
