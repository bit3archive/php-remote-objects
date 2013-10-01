<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Invocation;

/**
 * Class InvokerInterface
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
interface InvokerInterface
{
	/**
	 * @param mixed  $targetObject
	 * @param string $methodName
	 * @param array  $methodParams
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function invoke($methodName, array $methodParams);
}
