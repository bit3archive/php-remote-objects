<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Server;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Server
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
interface Server
{
	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function handle(Request $request);
}
