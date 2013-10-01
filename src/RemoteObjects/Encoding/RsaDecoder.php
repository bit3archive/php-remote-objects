<?php

/*
 * This file is part of the RemoteObjects library.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RemoteObjects\Encoding;

/**
 * Class RsaEncoder
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Encode
 * @api
 */
class RsaDecoder implements Decoder
{
	/**
	 * @var string
	 */
	protected $privateKey;

	/**
	 * The crypt component.
	 *
	 * @var \Crypt_RSA
	 */
	protected $rsa;

	function __construct($privateKey)
	{
		$this->privateKey = $privateKey;
	}

	/**
	 * @param string $localPrivateKey
	 */
	public function setPrivateKey($localPrivateKey)
	{
		$this->privateKey = $localPrivateKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrivateKey()
	{
		return $this->privateKey;
	}

	/**
	 * Get the crypt rsa component.
	 *
	 * @return \Crypt_RSA
	 */
	protected function getCryptRsa()
	{
		if ($this->rsa === null) {
			$this->rsa = new \Crypt_RSA();
			$this->rsa->loadKey($this->privateKey);
		}

		return $this->rsa;
	}

	public function decode($crypt)
	{
		$rsa    = $this->getCryptRsa();
		$string = $rsa->decrypt($crypt);
		return $string;
	}
}
