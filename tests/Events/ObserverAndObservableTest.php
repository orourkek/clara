<?php
/**
 * ObservableTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Events\Observable;
use Clara\Events\Observer;
use Clara\Events\Event;

class ObservableFoo extends Observable {
	public function foo() {
		$this->fire(new Event('foo.foo', $this));
	}
}
class ObserverFoo extends Observer {
	public $witnessed = array();
	public function witness(Event $event) {
		$this->witnessed[] = $event;
	}
}

class ObserverAndObservableTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Clara\Events\Observable::attach
	 */
	public function testAttach() {
		$foo = new ObservableFoo();
		$observer = new ObserverFoo();
		$foo->attach($observer);
		$this->assertAttributeContains($observer, 'observers', $foo);
		return $foo;
	}

	/**
	 * @covers Clara\Events\Observable::detatch
	 */
	public function testDetatch() {
		$foo = new ObservableFoo();
		$observer = new ObserverFoo();
		$foo->attach($observer);
		$foo->detatch($observer);
		$this->assertAttributeNotContains($observer, 'observers', $foo);
	}

	/**
	 * @covers Clara\Events\Observable::fire
	 */
	public function testFireAndWitness() {
		$foo = new ObservableFoo();
		$observer = new ObserverFoo();
		$foo->attach($observer);
		$event = new Event('foo.foo');
		$foo->fire($event);
		$this->assertAttributeContains($event, 'witnessed', $observer);
	}

}
 