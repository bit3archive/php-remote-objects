<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Encoding;

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
	 * Encode input.
	 *
	 * @param mixed $input
	 *
	 * @return mixed
	 */
	public function encode($input);
}
