<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Transport;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CurlClient
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Transport
 * @api
 */
class CurlClient implements Client, LoggerAwareInterface
{
	/**
	 * The logger facility.
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * The endpoint url.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * List of additional http headers.
	 *
	 * @var array
	 */
	protected $headers = array();

	/**
	 * The curl resource.
	 *
	 * @var resource
	 */
	protected $curl;

	/**
	 * @param string $url The endpoint url.
	 * @param array  $headers Additional http headers.
	 */
	function __construct($url, array $headers = array())
	{
		$this->url     = $url;
		$this->headers = $headers;
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

	protected function getCurl()
	{
		if ($this->curl === null) {
			if ($this->logger !== null) {
				$this->logger->debug(
					'Creating new curl instance',
					array(
						 'endpoint' => $this->url,
						 'headers'  => $this->headers
					)
				);
			}

			// create curl instance
			$this->curl = curl_init();

			// set the request url
			curl_setopt($this->curl, CURLOPT_URL, $this->url);

			// set transfer to binary (for encrypted data)
			curl_setopt($this->curl, CURLOPT_BINARYTRANSFER, true);

			// set method to POST
			curl_setopt($this->curl, CURLOPT_POST, true);

			// set headers
			curl_setopt($this->curl, CURLOPT_HEADER, $this->headers);
		}

		return $this->curl;
	}

	public function request($json)
	{
		if ($this->logger !== null) {
			$this->logger->debug(
				'Do curl request',
				array(
					 'endpoint' => $this->url,
					 'headers'  => $this->headers,
					 'post'     => $json
				)
			);
		}

		$curl = $this->getCurl();

		// create a temporary file, to store the response in it
		$responseStream = fopen('php://temp', 'w+');

		// set the request body
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

		// set the response output file
		curl_setopt($curl, CURLOPT_FILE, $responseStream);

		// exec request
		$success = (bool) curl_exec($curl);

		// read response from temporary file
		rewind($responseStream);
		$response = stream_get_contents($responseStream);

		// close temporary files
		fclose($responseStream);

		if (!$success) {
			if ($this->logger !== null) {
				$this->logger->error(
					'Requesting ' . $this->url . ' failed with ' . curl_error($curl),
					array(
						 'endpoint' => $this->url,
						 'headers'  => $this->headers
					)
				);
			}

			throw new \Exception(
				'Requesting ' . $this->url . ' failed with ' . curl_error($curl) . '!',
				curl_errno($curl)
			);
		}

		// read status code
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		// if request not success...
		if ($httpCode != 200) {
			if ($this->logger !== null) {
				$this->logger->error(
					'Requesting ' . $this->url . ' failed with HTTP ' . $httpCode,
					array(
						 'endpoint' => $this->url,
						 'headers'  => $this->headers
					)
				);
			}

			// ...throw an exception
			throw new \Exception(
				'Requesting ' . $this->url . ' failed with HTTP ' . $httpCode,
				$httpCode
			);
		}

		return $response;
	}
}
