<?php
namespace Randomizer\Database;

use PDO;
use PDOException;

use Randomizer\Exception\RuntimeException;
use Randomizer\Database\Credential;

/**
 * Connections manager
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class Manager
{

	/**
	 * @var Manager
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected $connections = [];

	protected function __construct(){}

	/**
	 * @return Manager
	 */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * @param Credential $credential
	 * @return Connection
	 */
	public function getConnection(Credential $credential)
	{
		$hash = $credential->getHash();
		if (!$this->hasConnection($credential)) {
			$this->connections[$hash] = $this->createConnection($credential);
		}
		return $this->connections[$hash];
	}

	/**
	 * @param Credential $credential
	 * @return bool
	 */
	public function hasConnection(Credential $credential)
	{
		$hash = $credential->getHash();
		return array_key_exists($hash, $this->connections);
	}

	/**
	 * Execute query set
	 * @param Credential $credential
	 * @param array $set
	 * @throws RuntimeException
	 */
	public function exec(Credential $credential, array $set)
	{
		$connection = $this->getConnection($credential);

		$connection->beginTransaction();

		try {
			foreach ($set as $query) {
				$connection->query($query);
				if ($connection->errorMode()) {
					$error = $connection->getError();
					$message = sprintf('%s%s-----%squery:%s%s%s-----', $error['message'], PHP_EOL, PHP_EOL, PHP_EOL, $query, PHP_EOL);
					throw new PDOException($message, $error['code']);
				}
			}
		} catch (PDOException $e) {
			$connection->rollBack();
			$dns = $credential->getDns();
			throw new RuntimeException(sprintf('DB manager "%s": %s', $dns, $e->getMessage()), $e->getCode(), $e);
		}

		$connection->commit();
	}

	/**
	 * Return DB column type
	 * @param Credential $credential
	 * @param string $table
	 * @param string $colm
	 * @return array
	 */
	public function getColmType(Credential $credential, $table, $colm)
	{
		$connection = $this->getConnection($credential);
		$colmSchema = $connection->getColmSchema($table, $colm);
		return $colmSchema['type'];
	}

	/**
	 * Create UPDATE query
	 * @param string $table
	 * @param array $colms
	 * @return string
	 */
	public function updateQuery($table, array $colms)
	{
		$pairs = [];
		foreach ($colms as $colm => $randomizer) {
			$pairs[] = sprintf('%s=%s(%s)', $colm, $randomizer->getMethodName(), $colm);
		}

		$query = sprintf('
			UPDATE %s SET
			%s
			',
			$table, implode(', ', $pairs));
		return $query;
	}

	/**
	 * Create Connection from Credential
	 * @param Credential $credential
	 * @return Connection
	 * @throws RuntimeException
	 */
	protected function createConnection(Credential $credential)
	{
		$dns = $credential->getDns();
		$name = $credential->getName();
		$password = $credential->getPassword();

		try {
			$pdo = new PDO($dns, $name, $password);
			return $this->connectFromPdo($pdo);
		} catch (PDOException $e){
			throw new RuntimeException(sprintf('DB manager "%s": %s', $dns, $e->getMessage()), $e->getCode(), $e);
		}
	}

	/**
	 * Connect factory
	 * @param PDO $pdo
	 * @return \Randomizer\Database\AbstractConnect
	 */
	protected function connectFromPdo(PDO $pdo)
	{
		$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
		$className = sprintf('\\Randomizer\\Database\\%s\\Connect', ucfirst($driver));
		return new $className($pdo);
	}
}
