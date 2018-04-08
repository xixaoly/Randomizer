<?php
namespace Randomizer\Database\Mysql;

use PDO;

use Randomizer\Database\AbstractConnect;

/**
 * Connect
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class Connect extends AbstractConnect
{

	const SUCCESS_CODE = '00000';

	public function errorMode()
	{
		list($code, $line, $message) = $this->pdo->errorInfo();
		return $code !== self::SUCCESS_CODE;
	}

	public function getError()
	{
		list($code, $line, $message) = $this->pdo->errorInfo();
		return [
			'code'=>$code,
			'message'=>$message,
		];
	}

	/**
	 * Return table schema
	 * @param string $table
	 * @return array
	 */
	protected function loadSchemaFromDb($table)
	{
		$query = sprintf('DESC %s', $table);
		$result = $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
		$schema = [];
		foreach ($result as $row) {
			$schema[$row['Field']] = [
				'name'=>$row['Field'],
				'type'=>$row['Type'],
				'isNull'=>$row['Null'],
				'defaultValue'=>$row['Default'],
			];
		}
		return $schema;
	}
}
