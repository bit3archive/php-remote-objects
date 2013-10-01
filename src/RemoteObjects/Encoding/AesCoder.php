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
 * Class AesCoder
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @package RemoteObjects\Encode
 * @api
 */
class AesCoder implements Encoder, Decoder
{
	/**
	 * @var string
	 */
	protected $psk;

	/**
	 * The crypt component.
	 *
	 * @var \Crypt_AES
	 */
	protected $aes;

	/**
	 * @param string $psk The pre-shared key.
	 */
	function __construct($psk)
	{
		$this->psk = (string) $psk;
	}

	/**
	 * @param string $psk
	 */
	public function setPsk($psk)
	{
		$this->psk = $psk;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPsk()
	{
		return $this->psk;
	}

	/**
	 * Get the crypt aes component.
	 *
	 * @return \Crypt_AES
	 */
	protected function getCryptAes()
	{
		if ($this->aes === null) {
			$this->aes = new \Crypt_AES();
			$this->aes->setKey($this->psk);
		}

		return $this->aes;
	}

	public function encode($string)
	{
		$aes = $this->getCryptAes();

		$crypt = $aes->encrypt($string);

		return $crypt;
	}

	public function decode($crypt)
	{
		$aes = $this->getCryptAes();

		$string = $aes->decrypt($crypt);

		return $string;
	}
}
