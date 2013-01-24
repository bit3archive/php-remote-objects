<?php

namespace RemoteObjects\Test;

interface EchoInterface
{
	public function reply($message);

	public function replyArray(array $messages);

	public function replyObject(\stdClass $messages);

	public function replyDefault($messages = 'whoozaaa');

	public function replyReference(&$messages);

	public function replyCombined(EchoInterface $echo = null, array &$messages = array());
}