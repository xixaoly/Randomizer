<?php
namespace Randomizer;

use Randomizer\Database\Manager;
use Randomizer\Utils\Job;
use Randomizer\Factory\JobFactory;

/**
 * Randomizer
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class Randomizer
{

	/**
	 * @var Manager
	 */
	protected $manager;

	/**
	 * @var JobFactory
	 */
	protected $jobFactory;

	public function __construct()
	{
		$this->manager = Manager::getInstance();
		$this->jobFactory = new JobFactory($this->manager);
	}

	/**
	 * Create Job from file
	 * @param string $path
	 * @return Job
	 */
	public function createJobFromFile($path)
	{
		return $this->jobFactory->createFromFile($path);
	}

	/**
	 * Create Job from directory
	 * @param string $dir
	 * @return array
	 */
	public function createJobsFromDir($dir)
	{
		return $this->jobFactory->createFromDir($dir);
	}

	public function install(Job $job)
	{
		$job->build();
		$job->install();
	}

	public function run(Job $job)
	{
		$job->runInstalled();
	}

	public function uninstall(Job $job)
	{
		$job->uninstall();
	}
}
