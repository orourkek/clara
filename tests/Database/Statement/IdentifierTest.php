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
		$name = 'foo';
		$alias = 'alias';
		$prefix = 'prefix';
		$id = new Identifier($name, $alias, $prefix);

		$this->assertAttributeSame($name, 'name', $id);
		$this->assertAttributeSame($alias, 'alias', $id);
		$this->assertAttributeSame($prefix, 'prefix', $id);

		return $id;
	}

	/**
	 * @covers Clara\Database\Statement\Identifier::getName
	 * @covers Clara\Database\Statement\Identifier::getAlias
	 * @covers Clara\Database\Statement\Identifier::getPrefix
	 * @covers Clara\Database\Statement\Identifier::setName
	 * @covers Clara\Database\Statement\Identifier::setAlias
	 * @covers Clara\Database\Statement\Identifier::setPrefix
	 * @depends testBasicCreate
	 */
	public function testGettersAndSetters(Identifier $id) {
		$name = 'bar';
		$prefix = 'b';
		$alias = 'baz';

		$id->setName($name);
		$id->setAlias($alias);
		$id->setPrefix($prefix);

		$this->assertAttributeSame($name, 'name', $id);
		$this->assertAttributeSame($alias, 'alias', $id);
		$this->assertAttributeSame($prefix, 'prefix', $id);

		$this->assertSame($name, $id->getName());
		$this->assertSame($alias, $id->getAlias());
		$this->assertSame($prefix, $id->getPrefix());
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

	public function provideStringsForTesting() {
		return array(
			array('`foo`.`bar` as `baz`', 'foo', 'bar', 'baz'),
			array('`foo`.`bar` AS `baz`', 'foo', 'bar', 'baz'),
			array('foo.bar AS baz', 'foo', 'bar', 'baz'),
			array('bar AS baz', '', 'bar', 'baz'),
			array('foo.bar', 'foo', 'bar', ''),
			array('bar', '', 'bar', ''),
			array('`bar`', '', 'bar', ''),
		);
	}

	/**
	 * @covers Clara\Database\Statement\Identifier::fromString
	 * @dataProvider provideStringsForTesting
	 */
	public function testFromString($string, $prefix, $name, $alias) {
		$id = Identifier::fromString($string);
		$this->assertAttributeSame($prefix, 'prefix', $id);
		$this->assertAttributeSame($name, 'name', $id);
		$this->assertAttributeSame($alias, 'alias', $id);
	}

	/**
	 * @covers Clara\Database\Statement\Identifier::fromString
	 * @dataProvider provideInvalidNames
	 * @expectedException \Clara\Database\Statement\Exception\StatementException
	 */
	public function testFromStringThrowsExceptionOnInvalidInput($val) {
		Identifier::fromString($val);
	}

}
 