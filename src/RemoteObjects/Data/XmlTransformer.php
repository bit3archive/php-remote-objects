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
 * Class XmlTransformer
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects
 * @api
 */
class XmlTransformer implements DataTransformerInterface
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
		$doc  = new \DOMDocument();
		$root = static::transformToXml($doc, null, $data);
		$doc->appendChild($root);

		$response = Response::create($doc->saveXML(), $this->defaultStatus, $this->headers);

		if (!$response->headers->has('content-type')) {
			$response->headers->set('content-type', 'application/xml');
		}

		return $response;
	}

	static public function threadAsObject($data)
	{
		if (is_object($data) || is_array($data)) {
			foreach ($data as $key => $value) {
				if (is_numeric($key)) {
					return false;
				}
			}
			return true;
		}

		return false;
	}

	static public function threadAsMap($data)
	{
		if (is_object($data) || is_array($data) && array_is_assoc($data)) {
			return true;
		}

		return false;
	}

	static public function transformToXml(\DOMDocument $doc, \DOMElement $parentElement = null, $data)
	{
		if (static::threadAsObject($data)) {
			$object = $doc->createElement('object');

			foreach ($data as $key => $value) {
				$item = $doc->createElement($key);
				$item->appendChild(static::transformToXml($doc, $item, $value));
				$object->appendChild($item);
			}

			return $object;
		}
		if (static::threadAsMap($data)) {
			$map = $doc->createElement('map');

			foreach ($data as $key => $value) {
				$item = $doc->createElement('item');
				$item->setAttribute('key', $key);
				$item->appendChild(static::transformToXml($doc, $item, $value));
				$map->appendChild($item);
			}

			return $map;
		}
		else if (is_array($data)) {
			$list = $doc->createElement('list');

			foreach ($data as $value) {
				$item = $doc->createElement('item');
				$item->appendChild(static::transformToXml($doc, $item, $value));
				$list->appendChild($item);
			}

			return $list;
		}
		else if (!$parentElement) {
			return $doc->createElement('value', $data);
		}
		else {
			return $doc->createTextNode($data);
		}
	}
}
