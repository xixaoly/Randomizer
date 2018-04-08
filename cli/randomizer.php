<?php
define('APP_DIR', dirname(__DIR__));

$autoloadFile = APP_DIR . '/vendor/autoload.php';
if (file_exists($autoloadFile)) {
	$loader = include $autoloadFile;
	$loader->add('', APP_DIR . '/src', true);
	$loader->add('', APP_DIR . '/cli', true);
}

// tracy
use Tracy\Debugger;
Debugger::enable(Debugger::DEVELOPMENT);

// bootstrap
use Randomizer\Randomizer;
use Randomizer\CLI;

$randomizer = new Randomizer;

$app = new CLI($randomizer);
$app->addJobs(CLI::args2Paths($argv));
$app->run();