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
	 * Decode input.
	 *
	 * @param mixed $input
	 *
	 * @return mixed
	 */
	public function decode($input);
}
