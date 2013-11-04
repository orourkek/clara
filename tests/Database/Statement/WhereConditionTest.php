<?php

/**
 * WhereConditionTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Database\Statement\WhereClause;
use Clara\Database\Statement\Identifier;
use Clara\Database\Statement\Select;

class WhereConditionTest extends PHPUnit_Framework_TestCase {

	public function provideTargets() {
		return array(
			array('foo', false),
			array('`foo`', false),
			array('`foo`.`bar`', false),
			array(123, true),
			array(false, true),
			array(true, true),
			array(null, true),
			array(0, true),
			array('', true),
			array(array(), true),
			array(new StdClass, true),
		);
	}

	/**
	 * @covers Clara\Database\Statement\WhereCondition::__construct
	 * @covers Clara\Database\Statement\WhereCondition::setTarget
	 * @dataProvider provideTargets
	 */
	public function testSetTarget($target, $expectException) {
		if($expectException) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', 'WhereCondition target must be a valid identifier');
			new WhereClause($target);
		} else {
			$cond = new WhereClause($target);
			$expected = Identifier::fromString($target);
			$this->assertAttributeEquals($expected, 'target', $cond);
		}
	}

	public function provideOperators() {
		return array(
			array('=', false),
			array('>=', false),
			array('<=', false),
			array('>', false),
			array('<', false),
			array('<>', false),
			array('!=', false),
			array('IN', false),
			array('NOT IN', false),
			array('LIKE', false),
			array('NOT LIKE', false),
			array('BETWEEN', false),
			array('NOT BETWEEN', false),
			array('IS', false),
			array('IS NOT', false),
			array(123, true),
			array(false, true),
			array(true, true),
			array(null, true),
			array(0, true),
			array('', true),
		);
	}

	/**
	 * @covers Clara\Database\Statement\WhereCondition::setOperator
	 * @dataProvider provideOperators
	 */
	public function testSetOperator($operator, $expectException) {
		$cond = new WhereClause('foo');
		if($expectException) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', sprintf('Invalid WhereCondition operator: "%s"', $operator));
			$cond->setOperator($operator);
		} else {
			$cond->setOperator($operator);
			$this->assertAttributeSame($operator, 'operator', $cond);
		}
	}

	public function providePredicates() {
		$stmt = new Select();
		$stmt->column('foo')->from('bar')->where('baz = taz');
		return array(
			array('foo', false, new Identifier('foo')),
			array(':foo', false, ":foo"),
			array(123, false, "'123'"),
			array('123', false, "'123'"),
			array('?', false, "?"),
			array(0, false, "'0'"),
			array($stmt, false, "(SELECT `foo` FROM `bar` WHERE `baz` = `taz`)"),
			array(false, true),
			array(true, true),
			array(null, true),
			array('', true),
		);
	}

	/**
	 * @covers Clara\Database\Statement\WhereCondition::setPredicate
	 * @dataProvider providePredicates
	 */
	public function testSetPredicate($predicate, $expectException, $expected='') {
		$cond = new WhereClause('foo');
		if($expectException) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', sprintf('Invalid WhereCondition predicate. Expecting string|int|Statement, received %s', gettype($predicate)));
			$cond->setPredicate($predicate);
		} else {
			$cond->setPredicate($predicate);
			$this->assertAttributeEquals($expected, 'predicate', $cond);
		}
	}

	public function providePreceders() {
		return array(
			array('OR', false),
			array('AND', false),
			array(false, true),
			array(true, true),
			array(null, true),
			array('', true),
		);
	}

	/**
	 * @covers Clara\Database\Statement\WhereCondition::setPreceder
	 * @dataProvider providePreceders
	 */
	public function testSetPreceder($preceder, $expectException) {
		$condition = new WhereClause('foo');
		if($expectException) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', sprintf('Invalid WhereCondition preceder. Expecting AND|OR'));
			$condition->setPreceder($preceder);
		} else {
			$condition->setPreceder($preceder);
			$this->assertAttributeSame(strtoupper($preceder), 'preceder', $condition);
		}
	}

	public function provideCompleteConditionsAndExpectedStrings() {
		$foo = new Select();
		$foo->column('bar')->from('baz');
		return array(
			array(new WhereClause('foo', '=', '0'), "`foo` = '0'"),
			array(new WhereClause('foo', 'like', "'bar'"), "`foo` LIKE 'bar'"),
			array(new WhereClause('foo', 'in', $foo), "`foo` IN (SELECT `bar` FROM `baz`)"),
			array(new WhereClause('foo.bar', '>=', '?'), "`foo`.`bar` >= ?"),
			array(new WhereClause('foo.bar', '!=', ':baz'), "`foo`.`bar` != :baz"),
		);
	}

	/**
	 * @covers Clara\Database\Statement\WhereCondition::__toString
	 * @dataProvider provideCompleteConditionsAndExpectedStrings
	 * @todo: nested where conditions
	 */
	public function testToString($cond, $expected) {
		$this->assertSame($expected, (string) $cond);
	}

	public function provideStringsForTesting() {
		return array(
			array('foo.bar = baz.taz', new WhereClause('foo.bar', '=', 'baz.taz')),
			array('foo <> 1', new WhereClause('foo', '<>', '1')),
			array('foo <= ?', new WhereClause('foo', '<=', '?')),
			array('foo=1', false),
			array('foo.bar= baz.taz', false),
			array('', false),
			array(false, false),
			array(true, false),
			array(null, false),
			array(array(), false),
			array(3.14159, false),
		);
	}

	/**
	 * @covers Clara\Database\Statement\WhereCondition::fromString
	 * @dataProvider provideStringsForTesting
	 */
	public function testFromString($str, $expected) {
		if(false === $expected) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', sprintf('WhereCondition::fromString failed with input "%s"', $str));
			WhereClause::fromString($str);
		} else {
			$cond = WhereClause::fromString($str);
			$this->assertEquals($expected, $cond);
		}
	}

}
 