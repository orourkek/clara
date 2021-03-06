<?php

/**
 * ConditionalExpressionTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Database\Statement\ConditionalExpression;
use Clara\Database\Statement\Identifier;
use Clara\Database\Statement\Select;

class WhereClauseTest extends PHPUnit_Framework_TestCase {

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
	 * @covers \Clara\Database\Statement\WhereClause::__construct
	 * @covers \Clara\Database\Statement\WhereClause::setTarget
	 * @dataProvider provideTargets
	 */
	public function testSetTarget($target, $expectException) {
		if($expectException) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', 'ConditionalExpression target must be a valid identifier');
			new ConditionalExpression($target);
		} else {
			$cond = new ConditionalExpression($target);
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
	 * @covers \Clara\Database\Statement\WhereClause::setOperator
	 * @dataProvider provideOperators
	 */
	public function testSetOperator($operator, $expectException) {
		$cond = new ConditionalExpression('foo');
		if($expectException) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', sprintf('Invalid ConditionalExpression operator: "%s"', $operator));
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
	 * @covers \Clara\Database\Statement\WhereClause::setPredicate
	 * @dataProvider providePredicates
	 */
	public function testSetPredicate($predicate, $expectException, $expected='') {
		$cond = new ConditionalExpression('foo');
		if($expectException) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', sprintf('Invalid ConditionalExpression predicate. Expecting string|int|Statement, received %s', gettype($predicate)));
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
	 * @covers \Clara\Database\Statement\WhereClause::setPreceder
	 * @dataProvider providePreceders
	 */
	public function testSetPreceder($preceder, $expectException) {
		$condition = new ConditionalExpression('foo');
		if($expectException) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', sprintf('Invalid ConditionalExpression preceder. Expecting AND|OR'));
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
			array(new ConditionalExpression('foo', '=', '0'), "`foo` = '0'"),
			array(new ConditionalExpression('foo', 'like', "'bar'"), "`foo` LIKE 'bar'"),
			array(new ConditionalExpression('foo', 'in', $foo), "`foo` IN (SELECT `bar` FROM `baz`)"),
			array(new ConditionalExpression('foo.bar', '>=', '?'), "`foo`.`bar` >= ?"),
			array(new ConditionalExpression('foo.bar', '!=', ':baz'), "`foo`.`bar` != :baz"),
		);
	}

	/**
	 * @covers \Clara\Database\Statement\WhereClause::__toString
	 * @dataProvider provideCompleteConditionsAndExpectedStrings
	 * @todo: nested where conditions
	 */
	public function testToString($cond, $expected) {
		$this->assertSame($expected, (string) $cond);
	}

	public function provideStringsForTesting() {
		return array(
			array('foo.bar = baz.taz', new ConditionalExpression('foo.bar', '=', 'baz.taz')),
			array('foo <> 1', new ConditionalExpression('foo', '<>', '1')),
			array('foo <= ?', new ConditionalExpression('foo', '<=', '?')),
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
	 * @covers \Clara\Database\Statement\WhereClause::fromString
	 * @dataProvider provideStringsForTesting
	 */
	public function testFromString($str, $expected) {
		if(false === $expected) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', sprintf('ConditionalExpression::fromString failed with input "%s"', $str));
			ConditionalExpression::fromString($str);
		} else {
			$cond = ConditionalExpression::fromString($str);
			$this->assertEquals($expected, $cond);
		}
	}

}
 