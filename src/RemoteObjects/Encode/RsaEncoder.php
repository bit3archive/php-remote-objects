<?php

namespace RemoteObjects\Encode;

class RsaEncoder extends CryptEncoder
{
	/**
	 * @var string
	 */
	protected $remotePublicKey;

	/**
	 * @var string
	 */
	protected $localPrivateKey;

	/**
	 * The crypt component.
	 *
	 * @var \Crypt_RSA
	 */
	protected $rsa;

	/**
	 * @param Encoder $encoder
	 * @param string  $remotePublicKey
	 * @param string  $localPrivateKey
	 */
	function __construct(Encoder $encoder, $remotePublicKey, $localPrivateKey)
	{
		parent::__construct($encoder);
		$this->remotePublicKey = $remotePublicKey;
		$this->localPrivateKey = $localPrivateKey;
	}

	/**
	 * @param string $remotePublicKey
	 */
	public function setRemotePublicKey($remotePublicKey)
	{
		$this->remotePublicKey = $remotePublicKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRemotePublicKey()
	{
		return $this->remotePublicKey;
	}

	/**
	 * @param string $localPrivateKey
	 */
	public function setLocalPrivateKey($localPrivateKey)
	{
		$this->localPrivateKey = $localPrivateKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLocalPrivateKey()
	{
		return $this->localPrivateKey;
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
		}

		return $this->rsa;
	}

	public function encrypt($string)
	{
		$rsa = $this->getCryptRsa();
		$rsa->loadKey($this->remotePublicKey);

		$crypt = $rsa->encrypt($string);

		return $crypt;
	}

	public function decrypt($crypt)
	{
		$rsa = $this->getCryptRsa();
		$rsa->loadKey($this->localPrivateKey);

		$string = $rsa->decrypt($crypt);

		return $string;
	}
}
