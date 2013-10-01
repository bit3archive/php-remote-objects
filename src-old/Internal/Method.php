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
 * Class Method
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
class Method implements MethodInterface
{
	/**
	 * The method name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The method parameters.
	 *
	 * @var array
	 */
	protected $parameters;

	function __construct($name, array $parameters = array())
	{
		$this->name      = (string) $name;
		$this->parameters = $parameters;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param array $parameters
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
}
