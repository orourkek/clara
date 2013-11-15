<?php
/**
 * LoggerObserverTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Events\Event;
use Clara\Events\Observable;
use Clara\Events\Observer\Logger;

class InterestingClass extends Observable {
	public function foo() {
		$this->fire(new Event('foo.bar', $this, array('baz' => 'taz')));
	}
}

class LoggerObserverTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers \Clara\Events\Observer\Logger::witness
	 * @covers \Clara\Events\Observer\Logger::constructMessage
	 */
	public function testObserving() {
		if( ! @mkdir('/tmp/clara') || false === touch('/tmp/clara/debug.log')) {
			$this->markTestSkipped('Could not complete Composer tests - sandbox environment creation failed. Running the test suite again, or manually deleting "/tmp/clara" might solve this issue.');
		}
		$observer = new Logger('/tmp/clara');

		$obj = new InterestingClass();
		$obj->attach($observer);
		$obj->foo();
		$content = file_get_contents('/tmp/clara/debug.log');

		$this->assertTrue(1 === preg_match('#^\[(.+?)\] Event "(.+?)" occurred in "(.+?)"#', $content));

		unlink('/tmp/clara/debug.log');
		rmdir('/tmp/clara');
	}

}
 