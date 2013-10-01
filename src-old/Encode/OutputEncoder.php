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
interface InputEncoder
{
	/**
	 * Encode a method name.
	 *
	 * @param string $methodName
	 *
	 * @return string
	 */
	public function encodeInput($input);
}
