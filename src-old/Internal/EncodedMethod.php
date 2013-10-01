<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Internal;

/**
 * Class EncodedMethod
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
class EncodedMethod implements EncodedMethodInterface
{
	/**
	 * @var Method|null
	 */
	protected $method;

	/**
	 * @var mixed|null
	 */
	protected $encoded;

	function __construct(Method $method = null, $encoded = null)
	{
		$this->method = $method;
		$this->encoded = $encoded;
	}

	/**
	 * @param Method|null $method
	 */
	public function setMethod(Method $method = null)
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param mixed|null $encoded
	 */
	public function setEncoded($encoded)
	{
		$this->encoded = $encoded;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEncodedMethod()
	{
		return $this->encoded;
	}
}
