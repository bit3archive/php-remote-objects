<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Data;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JsonTransformer
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
class JsonTransformer implements DataTransformerInterface
{
	/**
	 * @var int
	 */
	protected $defaultStatus;

	/**
	 * @var array
	 */
	protected $headers;

	function __construct($defaultStatus = 200, array $headers = array())
	{
		$this->defaultStatus = (int) $defaultStatus;
		$this->headers       = $headers;
	}

	/**
	 * @param int $defaultStatus
	 */
	public function setDefaultStatus($defaultStatus)
	{
		$this->defaultStatus = (int) $defaultStatus;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getDefaultStatus()
	{
		return $this->defaultStatus;
	}

	/**
	 * @param array $headers
	 */
	public function setHeaders(array $headers)
	{
		$this->headers = $headers;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * @param Request $request
	 * @param mixed   $data
	 *
	 * @return Response
	 */
	public function transform(Request $request, $data)
	{
		$response = JsonResponse::create(array(), $this->defaultStatus, $this->headers);
		$response->setData($data);
		return $response;
	}
}
