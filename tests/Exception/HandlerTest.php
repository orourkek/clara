<?php
/**
 * HandlerTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Exception\Handler;

class HandlerTest extends PHPUnit_Framework_TestCase {

	public function testCreate() {
		$handler = new Handler();
		$this->assertInstanceOf('\Clara\Exception\Handler', $handler);
	}

}
