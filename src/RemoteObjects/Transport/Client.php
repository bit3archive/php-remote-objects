<?php

namespace RemoteObjects\Transport;

interface Client
{
	public function request($json);
}