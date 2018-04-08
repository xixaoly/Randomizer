# Randomizer
Simply DB data randomize.

## Install
* `git clone git@github.com:xixaoly/Randomizer.git`
* `cd Randomizer`
* `composer update`

## CLI example
Create `example.yml` file with schema declaration and run `php cli/randomizer.php example.yml`

## PHP example
Include composer autoload (like `require 'vendor/autoload.php'`), create Job and handle it
```php
<?php
use Randomizer\Randomizer;

$randomizer = new Randomizer;
$job = $randomizer->createJobFromFile($path);

try {
	$randomizer->install($job);
	$randomizer->run($job);
	$randomizer->uninstall($job);
} catch (Exception $e) {
	$randomizer->uninstall($job);
}
```

## Schema example
```yml
connection:
    dns: mysql:dbname=randomizer;host=127.0.0.1
    name: root
    password: example
options:
    defaultClass:
        class: Randomizer\Database\Mysql\Method\RandomString
schema:
    table1:
        colm1:
            class: Randomizer\Database\Mysql\Method\RandomNumber
            arguments:
                min: 10
                max: 20
        colm2:
            class: Randomizer\Database\Mysql\Method\RandomString
        colm3:
```