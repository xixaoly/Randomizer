<?php
namespace Randomizer\Factory;

use Symfony\Component\Yaml\Yaml;

use Randomizer\Exception\IOException;
use Randomizer\Utils\Job;
use Randomizer\Database\Credential;
use Randomizer\Utils\Schema;
use Randomizer\Database\Manager;

/**
 * JobFactory
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class JobFactory
{

	/**
	 * @var Manager
	 */
	protected $manager;

	public function __construct(Manager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * Create Job from file
	 * @param string $path
	 * @return Job
	 * @throws IOException
	 */
	public function createFromFile($path)
	{
		if (!is_readable($path)) {
			throw new IOException(sprintf('File "%s" not found', $path));
		}
		// yaml file to array
		$yml = Yaml::parseFile($path);
		$credentials = new Credential(
				$yml[Job::KEY_CONNECTION]['dns'],
				$yml[Job::KEY_CONNECTION]['name'],
				$yml[Job::KEY_CONNECTION]['password']
				);
		// create schema
		$schema = new Schema($yml[Job::KEY_SCHEMA]);
		// create job
		$job = new Job($path, $credentials, $schema, $this->manager);

		// set options
		if (array_key_exists(Job::KEY_OPTIONS, $yml) and $yml[Job::KEY_OPTIONS]) {
			$job->addOptions($yml[Job::KEY_OPTIONS]);
		}

		return $job;
	}

	/**
	 * Create Job from directory
	 * @param string $dir
	 * @return array
	 * @throws IOException
	 */
	public function createFromDir($dir)
	{
		if (!is_dir($dir)) {
			throw new IOException(sprintf('Dir "%s" not found', $dir));
		}

		// scan dir
		$settings = [];
		$files = glob(sprintf('%s/*.yml', $dir));
		foreach ($files as $path) {
			$settings[] = self::createFromFile($path);
		}
		return $settings;
	}

}
