<?php
namespace Randomizer;

use Exception;

use Randomizer\Exception\RuntimeException;
use Randomizer\Utils\Job;

/**
 * CLI
 *
 * @author 2018 Adam Hladik <xixaoly@gmail.com>
 */
class CLI
{

	protected $randomizer;
	protected $jobs = [];

	public function __construct(Randomizer $randomizer)
	{
		$this->randomizer = $randomizer;
	}

	public function addJobs(array $paths = [])
	{
		foreach ($paths as $path) {
			if (is_dir($path)) {
				$this->jobs = array_merge($this->jobs, $this->randomizer->createJobsFromDir($path));
			} elseif (is_file($path)) {
				$this->jobs[] = $this->randomizer->createJobFromFile($path);
			} else {
				$this->output(sprintf('Path "%s" is not readable', $path));
			}
		}
	}

	public function run()
	{
		if (count($this->jobs)) {
			foreach ($this->jobs as $job) {
				$this->output(sprintf('Starting new job "%s"', $job->getName()));
				$this->handleJob($job);
			}
		} else {
			$this->output('Empty jobs queue');
		}
	}

	public function output($data)
	{
		echo $data . PHP_EOL;
	}

	protected function handleJob(Job $job)
	{
		try {
			$this->output(sprintf('Installing "%s"', $job->getName()));
			$this->randomizer->install($job);
			$this->output(sprintf('Running "%s"', $job->getName()));
			$this->randomizer->run($job);
			$this->output(sprintf('Uninstalling "%s"', $job->getName()));
			$this->randomizer->uninstall($job);
		} catch (RuntimeException $e) {
			$this->output(sprintf('Error: %s', $e->getMessage()));
			$this->output(sprintf('Uninstalling "%s"', $job->getName()));
			$this->randomizer->uninstall($job);
		} catch (Exception $e) {
			$this->output(sprintf('Error: %s', $e->getMessage()));
		}
	}

	public static function args2Paths(array $argv)
	{
		array_shift($argv);
		return array_map(function($path) {
			if ($path === '.') {
				return APP_DIR . '/';
			} elseif (file_exists($path)) {
				return $path;
			} else {
				return sprintf('%s/%s/', APP_DIR, $path);
			}
		}, $argv);
	}
}
