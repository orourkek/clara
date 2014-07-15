<?php
/**
 * Script.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <script> element
 *
 * @package Clara\Html\Element
 */
class Script extends Element {

	/**
	 * @var string
	 */
	protected $type = 'script';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input
	 */
	protected $allowedAttributes = array(
		'type',
		'src',
		'async'
	);

	/**
	 * Shortcut method for a common attribute for this element type
	 *
	 * @param $value
	 * @return $this
	 */
	public function type($value) {
		return $this->addAttribute('type', $value);
	}

	/**
	 * Shortcut method for a common attribute for this element type
	 *
	 * @param $value
	 * @return $this
	 */
	public function src($value) {
		return $this->addAttribute('src', $value);
	}

}