<?php
namespace Randomizer\Factory;

/**
 * MethodFactory
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class MethodFactory
{

	/**
	 * @var string
	 */
	protected $class;

	/**
	 * @var array
	 */
	protected $arguments = [];

	public function __construct($class, array $arguments=[])
	{
		$this->class = $class;
		$this->arguments = $arguments;
	}

	/**
	 * Create method with arguments
	 * @param string $type
	 * @return \Randomizer\Database\AbstractMethod
	 */
	public function createMethod($type)
	{
		$className = $this->class;
		$method = new $className($type);

		// set args
		foreach ($this->arguments as $key => $value) {
			$setter = \Randomizer\Utils\Helpers::getSetterName($key);
			if (method_exists($method, $setter)) {
				$method->$setter($value);
			} else {
				trigger_error(sprintf('Class "%s" hasn\'t setter "%s"', $className, $setter));
			}
		}

		return $method;
	}
}
