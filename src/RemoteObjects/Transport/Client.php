<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Transport;

/**
 * Class Client
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Transport
 * @api
 */
interface Client
{
	public function request($json);
}