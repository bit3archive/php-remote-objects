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
	 * @param string $remotePublicKey
	 * @param string $localPrivateKey
	 */
	function __construct(Encoder $encoder, $psk)
	{
		parent::__construct($encoder);
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
