<?php
/**
 * autoload.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

$foo = $thisShouldGenerateAWarningWithPhpStorm;

spl_autoload_register(function($className) {
	$className = ltrim($className, '\\');
	$fileName  = '';
	if ($lastNsPos = strripos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	$fullPath = dirname(__DIR__) . '/' . $fileName;
	if(is_readable($fullPath)) {
		require $fullPath;
		return true;
	} else {
		return false;
	}
});