<?php
/**
 * Attribute.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html;

use Clara\Support\Contract\Stringable;

/**
 * Represents an HTML element attribute, e.g. id="myElement"
 *
 * @package Clara\Html
 */
class Attribute implements Stringable {

	/**
	 * The attribute name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The attribute value
	 *
	 * @var string
	 */
	protected $value;

	/**
	 * @param $name
	 * @param $value
	 */
	public function __construct($name, $value) {
		$this->name = strtolower((string) $name);
		$this->value = (string) $value;
	}

	/**
	 * @param $value
	 * @return Attribute
	 */
	public function append($value) {
		$this->value .= ' ' . $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return sprintf('%s="%s"', $this->name, $this->value);
	}
} 