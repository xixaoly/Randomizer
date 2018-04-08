<?php
namespace Randomizer\Utils;

/**
 * Helpers
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class Helpers
{

	/**
	 * Return key from array or default
	 * @param string $key
	 * @param array $arr
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getKeyValue($key, array $arr, $default=null)
	{
		return array_key_exists($key, $arr)
			? $arr[$key]
			: $default;
	}

	public static function getSetterName($key)
	{
		return sprintf('set%s', ucfirst($key));
	}
}
