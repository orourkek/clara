<?php
/**
 * AmountTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\ECommerce\Amount;

class AmountTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Clara\ECommerce\Amount::__construct
	 */
	public function testCreate() {
		$amt = new Amount();
		$this->assertInstanceOf('Clara\ECommerce\Amount', $amt);
		$this->assertAttributeSame(0.0, 'value', $amt);
	}

	/**
	 * @covers Clara\ECommerce\Amount::__construct
	 */
	public function testRichConstructor() {
		$amt = new Amount(13.37);
		$this->assertInstanceOf('Clara\ECommerce\Amount', $amt);
		$this->assertAttributeSame(13.37, 'value', $amt);
	}

	/**
	 * @covers Clara\ECommerce\Amount::__toString
	 */
	public function testToString() {
		$amt = new Amount(13.37);
		$this->assertSame('13.37', (string)$amt);

		$amt = new Amount(9.999);
		$this->assertSame('10.00', (string)$amt);

		$amt = new Amount(10);
		$this->assertSame('10.00', (string)$amt);
	}

	public function provideInvalidValues() {
		return array(
			array(''),
			array('not numeric'),
			array(array()),
		);
	}

	/**
	 * @covers Clara\ECommerce\Amount::setValue
	 * @dataProvider provideInvalidValues
	 * @expectedException Clara\ECommerce\Exception\ValueException
	 */
	public function testInvalidValueThrowsException($value) {
		$amt = new Amount();
		$amt->setValue($value);
	}

	public function provideValuesAndExpectedResults() {
		return array(
			array(3.14159, 3.14),
			array(13.37, 13.37),
			array(13.99, 13.99),
			array(13.99999, 14.00),
			array(13.995, 14.00),
		);
	}

	/**
	 * @covers Clara\ECommerce\Amount::getValue
	 * @dataProvider provideValuesAndExpectedResults
	 */
	public function testGetValue($value, $expectedResult) {
		$amt = new Amount($value);
		$this->assertSame($expectedResult, $amt->getValue());
	}

	/**
	 * @covers Clara\ECommerce\Amount::merge
	 */
	public function testMerge() {
		$amt1 = new Amount(50.50);
		$amt2 = new Amount(25.25);

		$this->assertAttributeSame(50.50, 'value', $amt1);
		$amt1->merge($amt2);
		$this->assertAttributeSame(75.75, 'value', $amt1);
	}

	public function provideValuesToAdd() {
		return array(
			array(50.00, 100.00),
			array(50, 100.00),
			array(new Amount(50), 100.00),
		);
	}

	/**
	 * @covers Clara\ECommerce\Amount::add
	 * @dataProvider provideValuesToAdd
	 */
	public function testAdd($value, $expectedResult) {
		$amt = new Amount(50.00);
		$this->assertAttributeSame(50.00, 'value', $amt);
		$amt->add($value);
		$this->assertAttributeSame($expectedResult, 'value', $amt);
	}

	/**
	 * @covers Clara\ECommerce\Amount::lessThan
	 */
	public function testLessThan() {
		$amt = new Amount(50.00);
		$this->assertTrue($amt->lessThan(51));
		$this->assertFalse($amt->lessThan(50.00));
		$this->assertFalse($amt->lessThan(0));
		return $amt;
	}

	/**
	 * @covers Clara\ECommerce\Amount::lessThanOrEqual
	 * @depends testLessThan
	 */
	public function testLessThanOrEqual(Amount $amt) {
		$this->assertTrue($amt->lessThanOrEqual(51));
		$this->assertTrue($amt->lessThanOrEqual(50));
		$this->assertFalse($amt->lessThanOrEqual(0));
		return $amt;
	}

	/**
	 * @covers Clara\ECommerce\Amount::greaterThan
	 * @depends testLessThanOrEqual
	 */
	public function testGreaterThan(Amount $amt) {
		$this->assertTrue($amt->greaterThan(49.99));
		$this->assertFalse($amt->greaterThan(50));
		$this->assertFalse($amt->greaterThan(51));
		return $amt;
	}

	/**
	 * @covers Clara\ECommerce\Amount::greaterThanOrEqual
	 * @depends testGreaterThan
	 */
	public function testGreaterThanOrEqual(Amount $amt) {
		$this->assertTrue($amt->greaterThanOrEqual(49.99));
		$this->assertTrue($amt->greaterThanOrEqual(50));
		$this->assertFalse($amt->greaterThanOrEqual(51));
		return $amt;
	}

	/**
	 * @covers Clara\ECommerce\Amount::equalTo
	 * @depends testGreaterThanOrEqual
	 */
	public function testEqualTo(Amount $amt) {
		$this->assertTrue($amt->equalTo(49.99999999));
		$this->assertTrue($amt->equalTo(50));
		$this->assertFalse($amt->equalTo(51));
	}

}
