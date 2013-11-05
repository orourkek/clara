<?php
$phpunit = shell_exec('which phpunit');
if(empty($phpunit)) {
	die('phpunit not found, aborting.');
}

$toSkip = array('mock');

foreach(scandir(__DIR__ . '/tests') as $item) {
	if(in_array($item, $toSkip) || 0 === strpos($item, '.')) {
		continue;
	}
	$fullPath = sprintf('%s/tests/%s', __DIR__, $item);
	switch(true) {
		case is_dir($fullPath):
			printf('%s==================================================================================%s', PHP_EOL, PHP_EOL);
			printf('Running tests in %s...%s', $item, PHP_EOL);
			printf('==================================================================================%s', PHP_EOL);
			echo shell_exec(sprintf('phpunit -c phpunit.xml %s', $fullPath));
			break;
		case ('Test.php' === substr($item, -8)):
			printf('%s==================================================================================%s', PHP_EOL, PHP_EOL);
			printf('Running test %s...%s', $item, PHP_EOL);
			printf('==================================================================================%s', PHP_EOL);
			echo shell_exec(sprintf('phpunit -c phpunit.xml %s', $fullPath));
			break;
		default:
			printf('%s==================================================================================%s', PHP_EOL, PHP_EOL);
			printf('Skipped unknown item %s%s', $item, PHP_EOL);
			printf('==================================================================================%s', PHP_EOL);
			break;
	}
}