<?php
namespace Randomizer\Database;

/**
 * AbstractMethod
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
abstract class AbstractMethod
{

	const DESCRIPTORS = [];

	/**
	 * @var string
	 */
	protected $type;

	public function __construct($type)
	{
		$this->type = $type;
	}

	/**
	 * Return method install query
	 */
	abstract public function createInstallQuery();

	/**
	 * Return method uninstall query
	 */
	abstract public function createUninstallQuery();

	/**
	 * Method sign
	 * @return string
	 */
	public function getMethodSign()
	{
		$className = get_called_class();
		$tail = [];
		foreach ($className::DESCRIPTORS as $descriptor) {
			if (property_exists($this, $descriptor)) {
				$tail[$descriptor] = $this->$descriptor;
			}
		}
		return md5(sprintf('%s-%s:%s', get_called_class(), serialize($tail), $this->type));
	}

	/**
	 * Method sign
	 * @return string
	 */
	final public function __toString()
	{
		return $this->getMethodSign();
	}

	/**
	 * Generate method name
	 * @return string
	 */
	public function getMethodName()
	{
		$className = str_replace('\\', '_', get_called_class());
		$classType = preg_replace('/[^a-zA-Z]/i', '', $this->type);
		return sprintf('randomizer_%s_%s', strtolower($className), strtolower($classType));
	}
}
