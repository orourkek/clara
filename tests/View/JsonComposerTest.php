<?php
/**
 * JsonComposerTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\View\JsonComposer;

class JsonComposerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers \Clara\View\JsonComposer::createResponseObject
	 */
	public function testContentTypeHeaderIsSet() {
		$composer = new JsonComposer();
		$response = $composer->compose();
		$this->assertSame('application/json', $response->getHeader('Content-Type'));
	}

	/**
	 * @covers \Clara\View\JsonComposer::composeBody
	 */
	public function testComposeWithoutTemplatesReturnsJSONEncodedArrayOfData() {
		$composer = new JsonComposer();
		$composer->with('foo', 'bar');
		$composer->with('baz', 'taz');
		$response = $composer->compose();
		$expected = json_encode(array('foo' => 'bar', 'baz' => 'taz'));
		$this->assertSame($expected, $response->getBody());
	}

	public function setUp() {

	}

	/**
	 * function to recursively delete a directory. Used in tearDown() below.
	 */
	private function rrmdir($path) {
		foreach(glob($path . '/*') as $file) {
			if(is_dir($file))
				$this->rrmdir($file);
			else
				unlink($file);
		}
		rmdir($path);
	}

	/**
	 * called after every test in this class
	 */
	public function tearDown() {
		if(is_dir('/tmp/clara')) {
			$this->rrmdir('/tmp/clara');
		}
	}

	/**
	 * @covers \Clara\View\JsonComposer::composeBody
	 */
	public function testComposeWithTemplates() {
		//first, create the template file
		if( ! @mkdir('/tmp/clara') || false === file_put_contents('/tmp/clara/fooTemplate.php', '<div id="<?php echo $foo; ?>"></div>')) {
			$this->markTestSkipped('Could not complete Composer tests - sandbox environment creation failed. Running the test suite again, or manually deleting "/tmp/clara" might solve this issue.');
		}
		$composer = new JsonComposer();
		$composer->with('foo', 'FFOOOO');
		$composer->withTemplate('/tmp/clara/fooTemplate.php');
		$response = $composer->compose();
		$expected = json_encode(array('response' => '<div id="FFOOOO"></div>'));
		$this->assertSame($expected, $response->getBody());

		//get rid of the template file & directory
		unlink('/tmp/clara/fooTemplate.php');
		rmdir('/tmp/clara');
	}

}
 