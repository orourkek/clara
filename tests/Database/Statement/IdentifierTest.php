<?php
/**
 * IdentifierTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Database\Statement\Identifier;

class IdentifierTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Clara\Database\Statement\Identifier::__construct
	 */
	public function testBasicCreate() {
		$id = new Identifier('foo', 'alias', 'prefix');
		$this->assertAttributeSame('foo', 'name', $id);
		$this->assertAttributeSame('alias', 'alias', $id);
		$this->assertAttributeSame('prefix', 'prefix', $id);
	}

	public function provideInvalidNames() {
		return array(
			array(''),
			array(null),
			array(1234),
			array(3.14159),
			array(false),
			array(true),
			array(new StdClass),
		);
	}

	/**
	 * @covers Clara\Database\Statement\Identifier::__construct
	 * @dataProvider provideInvalidNames
	 * @expectedException \Clara\Database\Statement\Exception\StatementException
	 */
	public function testInvalidNamesThrowException($name) {
		$id = new Identifier($name);
	}

	public function provideIdentifiersAndExpectedStrings() {
		return array(
			array('foo', '', '', '`foo`'),
			array('bar', 'barAlias', '', '`bar` AS `barAlias`'),
			array('baz', 'bazAlias', 'bazPrefix', '`bazPrefix`.`baz` AS `bazAlias`'),
		);
	}

	/**
	 * @covers Clara\Database\Statement\Identifier::__toString
	 * @dataProvider provideIdentifiersAndExpectedStrings
	 */
	public function testToString($name, $alias, $prefix, $expected) {
		$id = new Identifier($name, $alias, $prefix);
		$this->assertSame($expected, (string)$id);
	}

	public function provideIdentifierNamesForTesting() {
		return array(
			array('', false, false),
			array('1234567890123456789012345678901234567890123456789012345678901234567890', false, false), //over 64 chars
			array('1234567890', false, true),
			array('abc$_123', false, true),
			array(str_repeat('a', 257), true, false),
			array(str_repeat('a', 255), true, true),
		);
	}

	/**
	 * @covers Clara\Database\Statement\Identifier::isValid
	 * @dataProvider provideIdentifierNamesForTesting
	 */
	public function testStaticIdentifierValidation($name, $isAlias, $expected) {
		$this->assertSame($expected, Identifier::isValid($name, $isAlias));
	}

}
 