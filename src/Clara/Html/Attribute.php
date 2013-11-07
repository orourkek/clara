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


/**
 * Represents an HTML element attribute, e.g. id="myElement"
 *
 * @package Clara\Html
 */
class Attribute {

	/**
	 * @var
	 */
	protected $name;

	/**
	 * @var
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
	 * @return string
	 */
	public function __toString() {
		return sprintf('%s="%s"', $this->name, $this->value);
	}
} 