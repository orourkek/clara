<?php
/**
 * SessionTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Storage\Session;


class SessionTest extends PHPUnit_Framework_TestCase {

	/**
	 * called before every test in this class - creates a sandbox $_SESSION for the tests to operate in
	 *
	 * NOTE: DO NOT CHANGE THIS METHOD WITHOUT KNOWING EXACTLY WHAT YOU'RE DOING.
	 *       MANY TESTS DEPEND ON THE SPECIFIC CONTENT ASSIGNED INTO THESE FILES,
	 *       SO PLEASE BE CAREFUL.
	 */
	public function setUp() {
		$_SESSION['foo'] = array(
			'bar' => array(
				'baz' => 'taz'
			),
			'rab' => 'zab',
		);
		$_SESSION['pi'] = 3.14159;
	}

	/**
	 * called after every test in this class
	 */
	public function tearDown() {
		unset($_SESSION);
	}

	/**
	 * @covers \Clara\Storage\Session::get
	 */
	public function testGetFirstLevel() {
		$this->assertSame(3.14159, Session::get('pi'));
	}

	/**
	 * @covers \Clara\Storage\Session::get
	 */
	public function testGetWithDotNotation() {
		$this->assertSame('taz', Session::get('foo.bar.baz'));
		$this->assertSame(array('baz' => 'taz'), Session::get('foo.bar'));
	}

	public function provideNonexistentKeys() {
		return array(
			array('zzz'),
			array(''),
			array(999),
			array('foo.bar.baz.taz.zap.zam'),
			array('foo.bar.baz.taz'),
		);
	}

	/**
	 * @covers \Clara\Storage\Session::get
	 * @dataProvider provideNonexistentKeys
	 */
	public function testGetNonexistentKeyReturnsNull($key) {
		$this->assertSame(null, Session::get($key));
	}

	public function provideInvalidKeys() {
		return array(
			array(null),
			array(array()),
			array(true),
			array(false),
			array(3.14159),
			array(new StdClass),
		);
	}

	/**
	 * @covers \Clara\Storage\Session::get
	 * @dataProvider provideInvalidKeys
	 * @expectedException \Clara\Exception\ClaraInvalidArgumentException
	 */
	public function testGetWithInvalidKeyThrowsException($key) {
		Session::get($key);
	}

	/**
	 * @covers \Clara\Storage\Session::set
	 */
	public function testSingleLevelSet() {
		Session::set('hello', 'world');
		$this->assertArrayHasKey('hello', $_SESSION);
		$this->assertSame('world', $_SESSION['hello']);
	}

	/**
	 * @covers \Clara\Storage\Session::set
	 */
	public function testSetWithDotNotation() {
		Session::set('one.two.three.four.five.six.seven', 'eight');
		$expected = array(
			'two' => array(
				'three' => array(
					'four' => array(
						'five' => array(
							'six' => array(
								'seven' => 'eight'
							)
						)
					)
				)
			)
		);
		$this->assertSame($expected, $_SESSION['one']);
	}

	/**
	 * @covers \Clara\Storage\Session::set
	 * @dataProvider provideInvalidKeys
	 * @expectedException \Clara\Exception\ClaraInvalidArgumentException
	 */
	public function testSetWithInvalidKeyThrowsException($key) {
		Session::set($key, '');
	}

	/**
	 * @covers \Clara\Storage\Session::has
	 */
	public function testBasicHas() {
		$this->assertTrue(Session::has('foo'));
		$this->assertFalse(Session::has('zzz'));
	}

	/**
	 * @covers \Clara\Storage\Session::has
	 */
	public function testHasWithDotNotation() {
		$this->assertTrue(Session::has('foo.bar.baz'));
		$this->assertFalse(Session::has('foo.bar.baz.zzz.zzz'));
		$this->assertFalse(Session::has('zzz.zzz'));
	}

	/**
	 * @covers \Clara\Storage\Session::has
	 * @dataProvider provideInvalidKeys
	 * @expectedException \Clara\Exception\ClaraInvalidArgumentException
	 */
	public function testHasWithInvalidKeyThrowsException($key) {
		Session::has($key);
	}

	/**
	 * @covers \Clara\Storage\Session::delete
	 */
	public function testBasicDelete() {
		$this->assertNotEmpty($_SESSION['foo']);
		Session::delete('foo');
		//Top level deletes (no dot notation) use unset() because no references are used.
		// See \Clara\Storage\Session::delete docblock for more info
		$this->assertFalse(isset($_SESSION['foo']));
	}

	/**
	 * @covers \Clara\Storage\Session::delete
	 */
	public function testDeleteWithDotNotation() {
		$this->assertNotEmpty($_SESSION['foo']['bar']);
		Session::delete('foo.bar');
		$this->assertEmpty($_SESSION['foo']['bar']);
		//make sure the other content at this level in the array is untouched
		$this->assertNotEmpty($_SESSION['foo']['rab']);
	}

	/**
	 * @covers \Clara\Storage\Session::delete
	 */
	public function testDeleteOnlyDeletesSpecifiedArrayKey() {
		$this->assertNotEmpty($_SESSION['foo']['rab']);
		Session::delete('foo.bar');
		$this->assertNotEmpty($_SESSION['foo']['rab']);
	}

	/**
	 * delete() will not actually do anything if the key doesn't exist
	 *
	 * @covers \Clara\Storage\Session::delete
	 */
	public function testDeleteNonexistentKeyThrowsNoErrors() {

	}

}
 