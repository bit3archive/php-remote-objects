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
 * Class EncodedException
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
class EncodedException implements EncodedExceptionInterface
{
	/**
	 * @var MethodException
	 */
	protected $methodException;

	/**
	 * @var mixed
	 */
	protected $encodedMethodException;

	/**
	 * @param MethodException $methodException
	 * @param mixed|null      $encodedMethodException
	 */
	function __construct(MethodException $methodException = null, $encodedMethodException = null)
	{
		$this->methodException        = $methodException;
		$this->encodedMethodException = $encodedMethodException;
	}

	/**
	 * @param \RemoteObjects\Internal\MethodException $methodException
	 */
	public function setMethodException($methodException)
	{
		$this->methodException = $methodException;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMethodException()
	{
		return $this->methodException;
	}

	/**
	 * @param mixed $encodedMethodException
	 */
	public function setEncodedMethodException($encodedMethodException)
	{
		$this->encodedMethodException = $encodedMethodException;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEncodedMethodException()
	{
		return $this->encodedMethodException;
	}
}
