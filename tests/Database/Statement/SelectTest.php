<?php
/**
 * SelectTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Database\Statement\Select;
use Clara\Database\Statement\Identifier;
use Clara\Database\Statement\ConditionalExpression;
use Clara\Database\Statement\OrderClause;

class SelectTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers \Clara\Database\Statement\Select::column
	 */
	public function testAddingSingleColumn() {
		$stmt = new Select();
		$this->assertAttributeEmpty('columns', $stmt);
		$stmt->column('foo');
		$expected = array(new Identifier('foo'));
		$this->assertAttributeEquals($expected, 'columns', $stmt);
	}

	/**
	 * @covers \Clara\Database\Statement\Select::columns
	 */
	public function testAddingMultipleColumns() {
		$stmt = new Select();
		$this->assertAttributeEmpty('columns', $stmt);
		$stmt->columns('foo', array('b.bar', 'baz'), 'taz');
		$expected = array(
			new Identifier('foo'),
			new Identifier('bar', 'baz', 'b'),
			new Identifier('taz'),
		);
		$this->assertAttributeEquals($expected, 'columns', $stmt);
	}

	/**
	 * @covers \Clara\Database\Statement\Select::from
	 */
	public function testFromWithAlias() {
		$stmt = new Select();
		$stmt->from('foo', 'alias');
		$expected = new Identifier('foo', 'alias');
		$this->assertAttributeEquals(array($expected), 'tables', $stmt);
	}

	public function provideWhereClauses() {
		$foo = new Select();
		$foo->column('bar')->from('baz');
		return array(
			array(array('foo.bar', '=', '0'), new ConditionalExpression('foo.bar', '=', 0)),
			array(array('foo.bar', '<>', 'baz.taz'), new ConditionalExpression('foo.bar', '<>', 'baz.taz')),
			array(array('bar', 'IN', $foo), new ConditionalExpression('bar', 'in', $foo)),
			array(array('bar'), new ConditionalExpression('bar')),
			array(array('bar', 'baz', 'taz', 'zap'), false),
		);
	}

	/**
	 * @covers \Clara\Database\Statement\Select::where
	 * @dataProvider provideWhereClauses
	 */
	public function testAddWhereClauses($args, $expectedResult) {
		$stmt = new Select();
		if(false === $expectedResult) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', 'Adding where condition failed');
			call_user_func_array(array($stmt, 'where'), $args);
		} else {
			call_user_func_array(array($stmt, 'where'), $args);
			$this->assertAttributeEquals(array($expectedResult), 'whereClauses', $stmt);
		}
	}

	public function provideLimits() {
		return array(
			array(10, null, '10'),
			array(10, 20, '10,20'),
			array(0, null, false),
			array(10, 0, false),
			array(-1, -1, false),
			array(null, null, false),
			array(true, true, false),
			array(array(), null, false),
			array(3.14159, null, false),
		);
	}

	/**
	 * @covers \Clara\Database\Statement\Select::limit
	 * @dataProvider provideLimits
	 */
	public function testLimit($arg1, $arg2, $expected) {
		$stmt = new Select();
		if(false === $expected) {
			$this->setExpectedException('\Clara\Database\Statement\Exception\StatementException', 'The limit clause requires one or two non-negative integer arguments');
			$stmt->limit($arg1, $arg2);
		} else {
			$stmt->limit($arg1, $arg2);
			$this->assertAttributeEquals($expected, 'limit', $stmt);
		}
	}

	/**
	 * @covers \Clara\Database\Statement\Select::orderBy
	 * @dataProvider provideLimits
	 */
	public function testOrderBy() {
		$stmt = new Select();
		$stmt->orderBy('foo.bar', 'DESC');
		$stmt->orderBy('baz.taz', 'ASC');
		$expected = array(
			new OrderClause('foo.bar', 'DESC'),
			new OrderClause('baz.taz', 'ASC'),
		);
		$this->assertAttributeEquals($expected, 'orderClauses', $stmt);
	}

	public function provideCompleteStatementsAndExpectedStrings() {
		$stmt1 = new Select();
		$stmt2 = new Select();
		$stmt3 = new Select();
		$stmt4 = new Select();

		return array(
			array(
				$stmt1->column('foo')->from('bar')->where('baz', '=', 'taz')->orWhere('baz', 'LIKE', '"taz"'),
				"SELECT `foo` FROM `bar` WHERE `baz` = `taz` OR `baz` LIKE \"taz\""
			),
			array(
				$stmt2->columns(array('f.foo', 'ffoo'), 'f.baz')->from('foo', 'f')->where('f.baz', '=', 'f.taz'),
				"SELECT `f`.`foo` AS `ffoo`, `f`.`baz` FROM `foo` AS `f` WHERE `f`.`baz` = `f`.`taz`"
			),
			array(
				$stmt3->from('foo', 'f')->orderBy('f.bar')->orderBy('f.baz', 'DESC'),
				"SELECT * FROM `foo` AS `f` ORDER BY `f`.`bar` ASC, `f`.`baz` DESC"
			),
			array(
				$stmt4->from('foo', 'f')->from('baz', 'b')->orderBy('b.uid')->limit(10,20),
				"SELECT * FROM `foo` AS `f`, `baz` AS `b` ORDER BY `b`.`uid` ASC LIMIT 10,20"
			),
		);
	}

	/**
	 * @covers \Clara\Database\Statement\Select::__toString
	 * @covers \Clara\Database\Statement\Select::compileStatement
	 * @dataProvider provideCompleteStatementsAndExpectedStrings
	 */
	public function testToString($statement, $expected) {
		$this->assertSame($expected, (string) $statement);
	}
}
 