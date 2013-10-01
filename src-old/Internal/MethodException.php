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
 * Class MethodException
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
class MethodException implements MethodExceptionInterface
{
	/**
	 * @var MethodInterface
	 */
	protected $method;

	/**
	 * @var \Exception
	 */
	protected $exception;

	function __construct(MethodInterface $method = null, \Exception $exception = null)
	{
		$this->method    = $method;
		$this->exception = $exception;
	}

	/**
	 * @param MethodInterface $method
	 */
	public function setMethod(MethodInterface $method)
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * Return the called method.
	 *
	 * @return MethodInterface
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param \Exception $exception
	 */
	public function setException(\Exception $exception)
	{
		$this->exception = $exception;
		return $this;
	}

	/**
	 * Return the result from the called method.
	 *
	 * @return \Exception
	 */
	public function getException()
	{
		return $this->exception;
	}
}
