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
 * Class MethodResult
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
class MethodResult implements MethodResultInterface
{
	/**
	 * The called method.
	 *
	 * @var Method
	 */
	protected $method;

	/**
	 * The result.
	 *
	 * @var mixed
	 */
	protected $result;

	function __construct(Method $method, $result)
	{
		$this->method = $method;
		$this->result = $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResult()
	{
		return $this->result;
	}
}
