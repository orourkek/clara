<?php
/**
 * phpunit-bootstrap.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

//if we don't include this, the PHP warnings about it will be converted to PHPUnit Exceptions and mucho tests will fail.
date_default_timezone_set('America/Los_Angeles');

require __DIR__ . '/src/Clara/autoload.php';