<?php
namespace Randomizer\Database;

/**
 * Credentials
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class Credential
{
	/**
	 * @var string
	 */
	protected $dns;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var string
	 */
	protected $hash;

	public function __construct($dns, $name, $password)
	{
		$this->dns = $dns;
		$this->name = $name;
		$this->password = $password;

		$this->generateHash();
	}

	/**
	 * @return string
	 */
	public function getDns()
	{
		return $this->dns;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getHash()
	{
		return $this->hash;
	}

	/**
	 * @return string
	 */
	protected function generateHash()
	{
		return md5(sprintf('%s:%s:%s', $this->dns, $this->name, $this->password));
	}
}
