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
class RsaEncoder implements Encoder
{
	/**
	 * @var string
	 */
	protected $publicKey;

	/**
	 * The crypt component.
	 *
	 * @var \Crypt_RSA
	 */
	protected $rsa;

	function __construct($publicKey)
	{
		$this->publicKey = $publicKey;
	}

	/**
	 * @param string $remotePublicKey
	 */
	public function setPublicKey($remotePublicKey)
	{
		$this->publicKey = $remotePublicKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPublicKey()
	{
		return $this->publicKey;
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
			$this->rsa->loadKey($this->publicKey);
		}

		return $this->rsa;
	}

	public function encode($string)
	{
		$rsa   = $this->getCryptRsa();
		$crypt = $rsa->encrypt($string);
		return $crypt;
	}
}
