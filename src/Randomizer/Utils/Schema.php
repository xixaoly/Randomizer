<?php
namespace Randomizer\Utils;

use Randomizer\Exception\SchemaException;
use Randomizer\Utils\Helpers;
use Randomizer\Factory\MethodFactory;

/**
 * Schema
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class Schema
{

	/**
	 * @var array
	 */
	protected $data=[];

	/**
	 * @var string
	 */
	protected $defaultClass;

	/**
	 * @var string
	 */
	protected $defaultArguments = [];

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @param array $defaultClass
	 */
	public function setDefaultClass(array $defaultClass)
	{
		$this->defaultClass = $defaultClass['class'];
		if (array_key_exists('arguments', $defaultClass)) {
			$this->defaultArguments = $defaultClass['arguments'];
		}
	}

	/**
	 * Return unique methods array
	 * @return array
	 */
	public function getMethods()
	{
		$methods = array_map([$this, 'getColmsMethod'], $this->data);
		return $methods;
	}

	/**
	 * Unique methods array filter
	 * @param array $colms
	 * @return array
	 * @throws SchemaException
	 */
	protected function getColmsMethod($colms)
	{
		$methods = [];
		foreach ($colms as $colmName => $colms) {
			if ($colms === null) {
				$colms = [];
			}
			$class = Helpers::getKeyValue('class', $colms, $this->defaultClass);
			if ($class) {
				$arguments = Helpers::getKeyValue('arguments', $colms, $this->defaultArguments);
				$methods[$colmName] = new MethodFactory($class, $arguments);
			} else {
				throw new SchemaException(sprintf('Colm "%s" has not defined class. Default class is not defined too.', $colmName));
			}
		}

		return $methods;
	}
}
