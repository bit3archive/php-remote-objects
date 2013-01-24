<?php

namespace RemoteObjects\Proxy;

use RemoteObjects\RemoteObject;

class RemoteObjectProxy implements RemoteObject
{
	/**
	 * @var \RemoteObjects\Client
	 */
	protected $client;

	/**
	 * @var string
	 */
	protected $path;

	function __construct($client, $path)
	{
		$this->client = $client;
		$this->path   = $path;
	}

	public function __call($name, $args)
	{
		return $this->client->invokeArgs(
			$this->path
				? $this->path . '.' . $name
				: $name,
			$args
		);
	}
}
