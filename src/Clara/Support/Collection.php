<?php
/**
 * Collection.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Support;

use Countable;
use ArrayAccess;
use IteratorAggregate;
use ArrayIterator;


/**
 * This is a generic data structure class, designed to be a stable API for data containers.
 *
 * I highly recommend NEVER TO MODIFY EXISTING METHODS IN THIS CLASS. Existing code likely
 * depends on the stability and specifics of this API, so changes to it could have
 * disastrous effects.
 *
 * You have been warned.
 *
 * Should you feel the need to heavily modify this class, please extend it or write a new class.
 *
 * @package Clara\Support
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate {

	/**
	 * @var array
	 */
	protected $items = array();

	/**
	 * @param array $items
	 */
	public function __construct(array $items = array()) {
		$this->items = $items;
	}

	/**
	 * @param mixed $options
	 * @return string
	 */
	public function toJson($options = 0) {
		return json_encode($this->toArray(), $options);
	}

	/**
	 * @return array
	 */
	public function toArray() {
		return $this->items;
	}

	/**
	 * @return bool
	 */
	public function isEmpty() {
		return empty($this->items);
	}

	/**
	 * @param int  $offset
	 * @param null $length
	 * @return array
	 */
	public function slice($offset, $length=null) {
		return array_slice($this->items, $offset, $length);
	}

	/**
	 * @return array
	 */
	public function keys() {
		return array_keys($this->items);
	}

	/**
	 * @return array
	 */
	public function values() {
		return array_values($this->items);
	}

	/**
	 * @param \Clara\Support\Collection|array $items
	 * @return $this
	 */
	public function merge($items) {
		if($items instanceof Collection) {
			$items = $items->toArray();
		}
		$this->items = array_merge($this->items, $items);
		return $this;
	}

	/**
	 * @return int
	 */
	public function count() {
		return count($this->items);
	}

	/**
	 * @param mixed $key
	 * @return bool
	 */
	public function offsetExists($key) {
		return array_key_exists($key, $this->items);
	}

	/**
	 * @param mixed $key
	 * @return mixed
	 */
	public function offsetGet($key) {
		return $this->items[$key];
	}

	/**
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function offsetSet($key, $value) {
		if (is_null($key)) {
			$this->items[] = $value;
		} else {
			$this->items[$key] = $value;
		}
	}

	/**
	 * @param mixed $key
	 */
	public function offsetUnset($key) {
		unset($this->items[$key]);
	}

	/**
	 * @return ArrayIterator
	 */
	public function getIterator(){
		return new ArrayIterator($this->items);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->toJson();
	}

}