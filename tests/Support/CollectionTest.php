<?php
/**
 * CollectionTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Support\Collection;

class CollectionTest extends PHPUnit_Framework_TestCase {

	protected $testData = array(
		0 => 'foo',
		1 => 'bar',
		2 => 'baz',
		3 => 'taz',
		4 => 'zat',
		5 => 'zab',
		6 => 'rab',
		7 => 'oof',
	);

	public function getTestCollection() {
		return new Collection($this->testData);
	}

	/**
	 * @covers Clara\Support\Collection::__construct
	 */
	public function testCreateEmptyCollection() {
		$coll = new Collection();
		$this->assertAttributeEmpty('items', $coll);
	}

	/**
	 * @covers Clara\Support\Collection::__construct
	 */
	public function testRichConstructorConstruction() {
		$data = array(
			'foo' => 'bar',
			'baz' => 'taz',
		);
		$coll = new Collection($data);
		$this->assertAttributeSame($data, 'items', $coll);
		return $coll;
	}

	/**
	 * @covers Clara\Support\Collection::toJson
	 */
	public function testToJson() {
		$coll = $this->getTestCollection();
		$this->assertSame($coll->toJson(), json_encode($this->testData));
	}

	/**
	 * @covers Clara\Support\Collection::toArray
	 */
	public function testToArray() {
		$coll = $this->getTestCollection();
		$returned = $coll->toArray();
		$this->assertInternalType('array', $returned);
		$this->assertSame($returned, $this->testData);
	}

	/**
	 * @covers Clara\Support\Collection::isEmpty
	 */
	public function testIsEmpty() {
		$coll = $this->getTestCollection();
		$this->assertFalse($coll->isEmpty());

		$coll2 = new Collection();
		$this->assertTrue($coll2->isEmpty());
	}

	/**
	 * @covers Clara\Support\Collection::slice
	 */
	public function testSlice() {
		$offset = 1;
		$length = 2;
		$coll = $this->getTestCollection();
		$expected = array_slice($this->testData, $offset, $length);
		$this->assertSame($expected, $coll->slice($offset, $length));
		//and again, with a negative value
		$offset = -2;
		$length = null;
		$coll = $this->getTestCollection();
		$expected = array_slice($this->testData, $offset, $length);
		$this->assertSame($expected, $coll->slice($offset, $length));
	}

	/**
	 * @covers Clara\Support\Collection::keys
	 */
	public function testKeys() {
		$coll = $this->getTestCollection();
		$this->assertSame($coll->keys(), array_keys($this->testData));
	}

	/**
	 * @covers Clara\Support\Collection::values
	 */
	public function testValues() {
		$coll = $this->getTestCollection();
		$this->assertSame($coll->values(), array_values($this->testData));
	}

	/**
	 * @covers Clara\Support\Collection::merge
	 */
	public function testMergeWithoutDuplicateKeys() {
		$coll = $this->getTestCollection();
		$toMerge = array(
			100 => 'hello',
			101 => 'world',
		);
		$coll->merge($toMerge);
		$this->assertAttributeContains('hello', 'items', $coll);
		$this->assertAttributeContains('world', 'items', $coll);
		$this->assertAttributeSame(array_merge($this->testData, $toMerge), 'items', $coll);
	}

	/**
	 * @covers Clara\Support\Collection::merge
	 */
	public function testMergeWithDuplicateNumericKeys() {
		$coll = $this->getTestCollection();
		$toMerge = array(
			1 => 'hello',
			2 => 'world',
		);
		$coll->merge($toMerge);
		$this->assertAttributeContains('hello', 'items', $coll);
		$this->assertAttributeContains('world', 'items', $coll);
		$this->assertAttributeSame(array_merge($this->testData, $toMerge), 'items', $coll);
	}

	/**
	 * @covers Clara\Support\Collection::count
	 */
	public function testCount() {
		$coll = $this->getTestCollection();
		$this->assertSame($coll->count(), count($this->testData));
	}


}
