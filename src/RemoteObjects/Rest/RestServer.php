<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Rest;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RemoteObjects\Data\DataTransformerInterface;
use RemoteObjects\Invocation\InvokerInterface;
use RemoteObjects\Server\Server;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * Class RestHttpServer
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Transport
 * @api
 */
class RestServer implements Server, LoggerAwareInterface
{
	/**
	 * The logger facility.
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @var UrlMatcherInterface
	 */
	protected $matcher;

	/**
	 * @var DataTransformerInterface
	 */
	protected $transformer;

	/**
	 * @var InvokerInterface
	 */
	protected $invoker;

	function __construct(UrlMatcherInterface $matcher, InvokerInterface $invoker, DataTransformerInterface $transformer)
	{
			$this->matcher = $matcher;
		$this->invoker = $invoker;
		$this->transformer = $transformer;
	}

	/**
	 * @param LoggerInterface $logger
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
		return $this;
	}

	/**
	 * @return LoggerInterface
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * @param \Symfony\Component\Routing\Matcher\UrlMatcherInterface $matcher
	 */
	public function setMatcher($matcher)
	{
		$this->matcher = $matcher;
		return $this;
	}

	/**
	 * @return \Symfony\Component\Routing\Matcher\UrlMatcherInterface
	 */
	public function getMatcher()
	{
		return $this->matcher;
	}

	/**
	 * @param \RemoteObjects\InvokerInterface $invoker
	 */
	public function setInvoker($invoker)
	{
		$this->invoker = $invoker;
		return $this;
	}

	/**
	 * @return \RemoteObjects\InvokerInterface
	 */
	public function getInvoker()
	{
		return $this->invoker;
	}

	/**
	 * @param DataTransformerInterface $transformer
	 */
	public function setTransformer(DataTransformerInterface $transformer)
	{
		$this->transformer = $transformer;
		return $this;
	}

	/**
	 * @return \RemoteObjects\Data\DataTransformerInterface
	 */
	public function getTransformer()
	{
		return $this->transformer;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle(Request $request)
	{
		$match = $this->matcher->match($request->getPathInfo());

		$parameters = array();

		if (isset($match['_method'])) {
			$methodName = $match['_method'];
		}
		else {
			$methodName = $match['_route'];
		}

		if (isset($match['_parameters'])) {
			foreach ((array) $match['_parameters'] as $parameterName) {
				if ($parameterName == '@request') {
					$parameters[] = $request;
				}
				else if ($parameterName == '@body') {
					$parameters[] = $request->getContent();
				}
				else if (isset($match[$parameterName])) {
					$parameters[] = $match[$parameterName];
				}
				else if ($parameterValue = $request->get($parameterName)) {
					$parameters[] = $parameterValue;
				}
				else {
					$parameters[] = null;
				}
			}
		}
		else {
			foreach ($match as $parameterName => $parameterValue) {
				if ($parameterName[0] != '_') {
					$parameters[] = $parameterValue;
				}
			}
		}

		$result = $this->invoker->invoke($methodName, $parameters);

		return $this->transformer->transform($request, $result);
	}
}
