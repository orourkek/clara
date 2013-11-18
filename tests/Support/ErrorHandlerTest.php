<?php
/**
 * HandlerTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Support\ErrorHandler;

class FooHandler extends ErrorHandler {
	public function handleError($level, $message, $file, $line, $context) {}
	public function handleException(Exception $exception) {}
}

class ErrorHandlerTest extends PHPUnit_Framework_TestCase {

	public function testCreate() {
		$handler = new FooHandler();
		$this->assertInstanceOf('\Clara\Support\ErrorHandler', $handler);
	}

}
