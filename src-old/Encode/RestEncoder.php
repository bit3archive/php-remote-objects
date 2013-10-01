<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Encode;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RestEncoder
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Encode
 * @api
 */
class RestEncoder extends LoggingEncoder
{
	/**
	 * @var UrlGeneratorInterface
	 */
	protected $urlGenerator;

	function __construct(UrlGeneratorInterface $urlGenerator = null)
	{
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * {@inheritdoc}
	 */
	public function encodeMethod($method, $params)
	{
		if (!$this->urlGenerator) {
			throw new \RuntimeException('Could not encode method, url generator is missing.');
		}


	}

	/**
	 * {@inheritdoc}
	 */
	public function encodeException(\Exception $exception)
	{
		// TODO: Implement encodeException() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function encodeResult($result)
	{
		// TODO: Implement encodeResult() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function decodeMethod($string)
	{
		// TODO: Implement decodeMethod() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function decodeResult($string)
	{
		// TODO: Implement decodeResult() method.
	}
}
