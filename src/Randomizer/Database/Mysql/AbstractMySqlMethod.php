<?php
namespace Randomizer\Database\Mysql;

use Randomizer\Database\AbstractMethod;

/**
 * AbstractMySqlMethod
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
abstract class AbstractMySqlMethod extends AbstractMethod
{

	/**
	 * Return method body
	 */
	abstract protected function getBody();

	/**
	 * Return method install query
	 * @return string
	 */
	public function createInstallQuery()
	{
		return sprintf('
			CREATE FUNCTION %s (value %s) RETURNS %s
			BEGIN
			%s
			END;
			',
			$this->getMethodName(), $this->type, $this->type, $this->getBody());
	}

	/**
	 * Return method uninstall query
	 * @return string
	 */
	public function createUninstallQuery()
	{
		return sprintf('DROP FUNCTION IF EXISTS %s;', $this->getMethodName());
	}
}
