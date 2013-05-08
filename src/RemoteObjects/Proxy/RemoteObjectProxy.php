<?php

namespace RemoteObjects\Proxy;

use RemoteObjects\RemoteObject;

class RemoteObjectProxy implements RemoteObject
{
	const NS_SEPARATOR = '__WS__';

	const NS_SEPARATOR_LENGTH = 6;

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

	public function __get($name)
	{
		return new RemoteObjectProxy(
			$this->client,
			$this->path
				? $this->path . '.' . $name
				: $name
		);
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
