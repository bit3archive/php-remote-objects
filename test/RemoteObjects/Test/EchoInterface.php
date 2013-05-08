<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Test;

/**
 * Class EchoInterface
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Test
 * @api
 */
interface EchoInterface
{
	public function reply($message);

	public function replyArray(array $messages);

	public function replyObject(\stdClass $messages);

	public function replyDefault($messages = 'whoozaaa');

	public function replyReference(&$messages);

	public function replyCombined(EchoInterface $echo = null, array &$messages = array());
}