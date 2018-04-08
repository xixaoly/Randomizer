<?php
namespace Randomizer\Database\Mysql\Method;

use Randomizer\Database\Mysql\AbstractMySqlMethod;

/**
 * RandomString
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class RandomString extends AbstractMySqlMethod
{

	const DESCRIPTORS = ['length'];

	protected $length = 10;

	public function setLength($length)
	{
		$this->length = (int)$length;
	}

	protected function getBody()
	{
		return sprintf('
				SELECT SUBSTRING(HEX(SHA2(CONCAT(NOW(), RAND(), UUID()), 512)) FROM 1 FOR %d) INTO value;
				RETURN value;
			', $this->length);
	}
}
