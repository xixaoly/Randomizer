<?php
namespace Randomizer\Database\Mysql\Method;

use Randomizer\Database\Mysql\AbstractMySqlMethod;

/**
 * RandomNumber
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class RandomNumber extends AbstractMySqlMethod
{

	const DESCRIPTORS = ['min', 'max'];

	protected $min = 1;
	protected $max = 10;

	public function setMin($min)
	{
		$this->min = (int)$min;
	}

	public function setMax($max)
	{
		$this->max = (int)$max;
	}

	protected function getBody()
	{
		return sprintf('
				SELECT FLOOR(RAND() * %d) + %d INTO value;
				RETURN value;
			', $this->min, $this->max);
	}
}
