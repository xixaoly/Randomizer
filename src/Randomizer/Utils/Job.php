<?php
namespace Randomizer\Utils;

use Randomizer\Database\Credential;
use Randomizer\Utils\Schema;
use Randomizer\Database\Manager;

/**
 * Job
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class Job
{
	const KEY_CONNECTION = 'connection',
			KEY_OPTIONS = 'options',
			KEY_SCHEMA = 'schema',
			KEY_DEFAULT_CLASS = 'defaultClass';

	protected $options = [
		self::KEY_DEFAULT_CLASS=>null,
	];

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var Credential
	 */
	protected $credential;

	/**
	 * @var Schema
	 */
	protected $schema;

	/**
	 * @var Manager
	 */
	protected $manager;

	/**
	 * @var array
	 */
	protected $installSet = [];

	/**
	 * @var array
	 */
	protected $runSet = [];

	/**
	 * @var array
	 */
	protected $uninstallSet = [];

	/**
	 * @var array
	 */
	protected $instaled = [];

	public function __construct(
		$name,
		Credential $credential,
		Schema $schema,
		Manager $manager
	) {
		$this->name = $name;
		$this->credential = $credential;
		$this->schema = $schema;
		$this->manager = $manager;
	}

	/**
	 * Return Job name
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Return Job credential
	 * @return Credential
	 */
	public function getCredential()
	{
		return $this->credential;
	}

	/**
	 * Set Job option
	 * @param array $options
	 * @param bool $replace
	 * @return $this
	 */
	public function addOptions(array $options, $replace=true)
	{
		if ($replace) {
			$this->options = $options;
		} else {
			$this->options = array_merge($this->options, $options);
		}
		return $this;
	}

	/**
	 * Create install, run and uninstall sets
	 */
	public function build()
	{
		$this->schema->setDefaultClass($this->options[self::KEY_DEFAULT_CLASS]);
		$methods = $this->schema->getMethods();

		$tablesColms = [];
		foreach ($methods as $table => $colms) {
			if (!array_key_exists($table, $tablesColms)) {
				$tablesColms[$table] = [];
			}

			foreach ($colms as $colm=>$methodFacotry) {
				$type = $this->manager->getColmType($this->credential, $table, $colm);
				$randomizer = $methodFacotry->createMethod($type);
				if (!array_key_exists((string)$randomizer, $this->installSet)) {
					$this->installSet[(string)$randomizer] = $randomizer->createInstallQuery();
					$this->uninstallSet[(string)$randomizer] = $randomizer->createUninstallQuery();
				}
				$tablesColms[$table][$colm] = $randomizer;
			}
		}

		foreach ($tablesColms as $table => $colms) {
			$this->runSet[] = $this->manager->updateQuery($table, $colms);
		}
	}

	/**
	 * Apply install set
	 */
	public function install()
	{
		if ($this->installSet) {
			$this->manager->exec($this->credential, $this->installSet);
			$this->instaled = array_keys($this->installSet);
		}
	}

	/**
	 * Apply run set
	 */
	public function runInstalled()
	{
		if ($this->runSet) {
			$this->manager->exec($this->credential, $this->runSet);
		}
	}

	/**
	 * Apply uninstall set
	 */
	public function uninstall()
	{
		if ($this->uninstallSet) {
			$set = array_filter($this->uninstallSet, [$this, 'filterUninstallSet'], ARRAY_FILTER_USE_KEY);
			$this->manager->exec($this->credential, $set);
		}
	}

	/**
	 * @param string $hash
	 * @return bool
	 */
	protected function filterUninstallSet($hash)
	{
		return in_array($hash, $this->instaled);
	}
}
