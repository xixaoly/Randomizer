<?php
namespace Randomizer\Database;

use PDO;

use Randomizer\Exception\SchemaException;

/**
 * AbstractConnect
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
abstract class AbstractConnect
{

	/**
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * @var array
	 */
	protected $tables = [];

	final public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Last query error state
	 */
	abstract public function errorMode();

	/**
	 * Last query error message
	 */
	abstract public function getError();

	/**
	 * Load table schema from DB
	 * @param string $table
	 */
	abstract protected function loadSchemaFromDb($table);

	/**
	 * @return PDO
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * Return table schema
	 * @param string $table
	 * @return array
	 */
	public function getTableSchema($table)
	{
		if (!array_key_exists($table, $this->tables)) {
			$this->tables[$table] = $this->loadSchemaFromDb($table);
		}
		return $this->tables[$table];
	}

	/**
	 * Return column schema
	 * @param string $table
	 * @param string $colm
	 * @return array
	 * @throws SchemaException
	 */
	public function getColmSchema($table, $colm)
	{
		$tableSchema = $this->getTableSchema($table);
		if (array_key_exists($colm, $tableSchema)) {
			return $tableSchema[$colm];
		} else
			throw new SchemaException(sprintf('Table "%s" has not colm "%s"', $table, $colm));
	}

	/**
	 * Call PDO methods
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array([$this->pdo, $name], $arguments);
	}
}
