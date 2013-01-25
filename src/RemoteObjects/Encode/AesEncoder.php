<?php

namespace RemoteObjects\Encode;

class AesEncoder extends CryptEncoder
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
	 * @param Encoder $encoder
	 * @param string $psk The pre-shared key.
	 * @param bool    $plainExceptions If encryption fails, return a plain unencrypted exception.
	 *                                 This is only useful on servers and for debugging.
	 */
	function __construct(Encoder $encoder, $psk, $plainExceptions = false)
	{
		parent::__construct($encoder, $plainExceptions);
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
	 * @return \Crypt_RSA
	 */
	protected function getCryptAes()
	{
		if ($this->aes === null) {
			$this->aes = new \Crypt_AES();
			$this->aes->setKey($this->psk);
		}

		return $this->aes;
	}

	public function encrypt($string)
	{
		$aes = $this->getCryptAes();

		$crypt = $aes->encrypt($string);

		return $crypt;
	}

	public function decrypt($crypt)
	{
		$aes = $this->getCryptAes();

		$string = $aes->decrypt($crypt);

		return $string;
	}
}
