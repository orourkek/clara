<?php
/**
 * WriterTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Logging\Writer;
use Clara\Logging\LogLevel;
use Clara\Events\Observer;
use Clara\Events\Event;

class LogObserver extends Observer {
	public $witnessed = array();
	public function witness(Event $event) {
		$this->witnessed[] = $event;
	}
	public function hasWitnessed($str) {
		foreach($this->witnessed as $w) {
			if($str === $w->getName()) {
				return true;
			}
		}
		return false;
	}
}


class WriterTest extends PHPUnit_Framework_TestCase {

	/**
	 * called before every test in this class - creates a temp sandbox directory for the tests to operate in
	 */
	public function setUp() {
		if(true !== @mkdir('/tmp/clara')) {
			$this->markTestSkipped('Could not complete log writer tests - sandbox environment creation failed. Running the test suite again, or manually deleting "/tmp/clara" might solve this issue.');
		}
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
	 * @covers \Clara\Logging\Writer::__construct
	 * @expectedException \Clara\Exception\ClaraRuntimeException
	 */
	public function testConstructorThrowsExceptionWithUnreadableDirectory() {
		$dir = '/this/surely/doesnt/exist';
		new Writer($dir);
	}

	/**
	 * @covers \Clara\Logging\Writer::__construct
	 */
	public function testCreate() {
		$w = new Writer('/tmp/clara');
	}

	public function provideLogLevels() {
		return array(
			array(LogLevel::EMERGENCY),
			array(LogLevel::ALERT),
			array(LogLevel::CRITICAL),
			array(LogLevel::DEBUG),
			array(LogLevel::ERROR),
			array(LogLevel::INFO),
			array(LogLevel::NOTICE),
			array(LogLevel::WARNING),
		);
	}

	/**
	 * @covers \Clara\Logging\Writer::log
	 * @dataProvider provideLogLevels
	 */
	public function testWrite($level) {
		$w = new Writer('/tmp/clara');
		$file = sprintf('/tmp/clara/%s.log', $level);
		$this->assertFileNotExists($file);
		$w->$level('foobarbaztaz!');
		$this->assertFileExists($file);
		$content = file_get_contents($file);
		$this->assertTrue(false !== strpos($content, 'foobarbaztaz!'));
	}

	/**
	 * @dataProvider provideLogLevels
	 */
	public function testEventFiring($level) {
		$w = new Writer('/tmp/clara');
		$obs = new LogObserver();
		$w->attach($obs);
		$w->$level('foobar');
		$this->assertTrue($obs->hasWitnessed(sprintf('log.%s', $level)));
	}
}
 