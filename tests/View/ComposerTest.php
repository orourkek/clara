<?php
/**
 * ComposerTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\View\Composer;

class FooComposer extends Composer {}

class ComposerTest extends PHPUnit_Framework_TestCase {

	/**
	 * called before every test in this class - creates a temp sandbox directory for the tests to operate in
	 *
	 * NOTE: DO NOT CHANGE THIS METHOD WITHOUT KNOWING EXACTLY WHAT YOU'RE DOING.
	 *       MANY TESTS DEPEND ON THE SPECIFIC CONTENT ASSIGNED INTO THESE FILES,
	 *       SO PLEASE BE CAREFUL.
	 */
	public function setUp() {
		if(@mkdir('/tmp/clara')) {
			if(false !== file_put_contents('/tmp/clara/fooTemplate.php', '<?php echo $foo; ?>')
				&& false !== file_put_contents('/tmp/clara/barTemplate.php', '<?php echo $bar; ?>')
			) {
				return true;
			}
		}
		$this->markTestSkipped('Could not complete Composer tests - sandbox environment creation failed. Running the test suite again, or manually deleting "/tmp/clara" might solve this issue.');
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
	 * @covers \Clara\View\Composer::__construct
	 * @covers \Clara\View\Composer::createResponseObject
	 */
	public function testConstructor() {
		$composer = new FooComposer();
		$this->assertInstanceOf('\Clara\View\Composer', $composer);
		$this->assertAttributeInstanceOf('\Clara\Storage\Filesystem', 'filesystem', $composer);
		$this->assertAttributeInstanceOf('\Clara\Http\Response', 'response', $composer);
	}

	/**
	 * @covers \Clara\View\Composer::setTemplatePath
	 * @expectedException \Clara\Storage\Exception\FileNotFoundException
	 */
	public function testInvalidTemplatePathThrowsException() {
		$composer = new FooComposer();
		$composer->setTemplatePath('/this/isnt/a/real/path');
	}

	/**
	 * @covers \Clara\View\Composer::setTemplatePath
	 */
	public function testSetTemplatePath() {
		$composer = new FooComposer();
		$composer->setTemplatePath('/tmp/clara');
		//there will be a trailing slash added
		$this->assertAttributeEquals('/tmp/clara/', 'templatePath', $composer);
	}

	/**
	 * @covers \Clara\View\Composer::with
	 */
	public function testAddingData() {
		$composer = new FooComposer();
		$composer->with('foo', 'bar');
		$this->assertAttributeEquals(array('foo' => 'bar'), 'data', $composer);
		return $composer;
	}

	/**
	 * @covers \Clara\View\Composer::with
	 * @depends testAddingData
	 */
	public function testAddingArrayOfDataMergesWithExisting(Composer $composer) {
		$composer->with(array(
			'foo' => 'oof',
			'bar' => 'baz',
		));
		//key 'foo' should have been overwritten
		$this->assertAttributeEquals(array('foo' => 'oof', 'bar' => 'baz'), 'data', $composer);
	}

	/**
	 * @covers \Clara\View\Composer::withTemplate
	 * @expectedException \Clara\Storage\Exception\FileNotFoundException
	 */
	public function testAddingNonexistentTemplateThrowsException() {
		$composer = new FooComposer();
		$composer->withTemplate('/asdf/asdf/asdf');
	}

	/**
	 * @covers \Clara\View\Composer::withTemplate
	 */
	public function testAddingTemplate() {
		$composer = new FooComposer();
		$composer->withTemplate('/tmp/clara/fooTemplate.php');
		$this->assertAttributeContains('/tmp/clara/fooTemplate.php', 'templates', $composer);
	}

	/**
	 * @covers \Clara\View\Composer::withTemplate
	 * @covers \Clara\View\Composer::setTemplatePath
	 */
	public function testAddingTemplateWithBasePath() {
		$composer = new FooComposer();
		$composer->setTemplatePath('/tmp/clara');
		$composer->withTemplate('fooTemplate.php');
		$this->assertAttributeContains('/tmp/clara/fooTemplate.php', 'templates', $composer);
	}

	/**
	 * @covers \Clara\View\Composer::compose
	 * @covers \Clara\View\Composer::composeBody
	 * @covers \Clara\View\Composer::setStatusCode
	 */
	public function testCompose() {
		$composer = new FooComposer();
		$composer->setStatusCode(500);
		$result = $composer->compose();
		$this->assertInstanceOf('\Clara\Http\Response', $result);
		$this->assertSame('', $result->getBody());
		$this->assertAttributeSame(500, 'statusCode', $result);
		$this->assertSame(0, $result->getHeader('Content-Length'));
		$this->assertSame(gmdate('D, d M Y H:i:s \G\M\T', time()), $result->getHeader('Date'));
	}

	/**
	 * @covers \Clara\View\Composer::compose
	 * @covers \Clara\View\Composer::composeBody
	 */
	public function testVariablesAreExposedToTemplates() {
		$composer = new FooComposer();
		$composer->with('foo', '>FOO<');
		$composer->with('bar', '>BAR<');
		$composer->withTemplate('/tmp/clara/fooTemplate.php');
		$composer->withTemplate('/tmp/clara/barTemplate.php');
		$result = $composer->compose();
		$this->assertInstanceOf('\Clara\Http\Response', $result);
		$this->assertSame('>FOO<>BAR<', $result->getBody());
		$this->assertSame(10, $result->getHeader('Content-Length'));
	}

}
 