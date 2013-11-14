<?php
/**
 * HtmlComposerTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\View\HtmlComposer;

class HtmlComposerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers \Clara\View\HtmlComposer::createResponseObject
	 */
	public function testContentTypeHeaderIsSet() {
		$composer = new HtmlComposer();
		$response = $composer->compose();
		$this->assertSame('text/html', $response->getHeader('Content-Type'));
	}

}
 