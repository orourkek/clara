<?php
/**
 * ControllerHandlerTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Routing\ControllerHandler;

class Foobar {
	public function baz() {}
}


class ControllerHandlerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers \Clara\Routing\ControllerHandler::__construct
	 * @expectedException \Clara\Exception\ClaraInvalidArgumentException
	 */
	public function testThatClassMustExistsOtherwiseExceptionIsThrown() {
		new ControllerHandler('this_class_doesnt_exist', 'method');
	}

	/**
	 * @covers \Clara\Routing\ControllerHandler::__construct
	 * @expectedException \Clara\Exception\ClaraInvalidArgumentException
	 */
	public function testThatMethodMustExistInClassOtherwiseExceptionIsThrown() {
		new ControllerHandler('FooBar', 'method');
	}

	/**
	 * @covers \Clara\Routing\ControllerHandler::__construct
	 */
	public function testThatConstructorDoesWhatItIsSupposedTo() {
		$ch = new ControllerHandler('FooBar', 'baz');
		$this->assertAttributeEquals('FooBar', 'clazz', $ch);
		$this->assertAttributeEquals('baz', 'method', $ch);
	}

	/**
	 * @covers \Clara\Routing\ControllerHandler::getCallable
	 */
	public function testCallableCreation() {
		$ch = new ControllerHandler('FooBar', 'baz');
		$callable = $ch->getCallable();
		$this->assertInternalType('callable', $callable);
		$this->assertEquals(array(new FooBar, 'baz'), $callable);
	}

}
 