<?php
/**
 * Label.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <label> element
 *
 * @package Clara\Html\Element
 */
class Label extends Element {

	/**
	 * @var string
	 */
	protected $type = 'label';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/label
	 */
	protected $allowedAttributes = array(
		'accesskey',
		'for',
		'form',
	);

	/**
	 * Shortcut method for a common attribute for this element type
	 *
	 * NOTE: The extra "r" is because "for" is a reserved word
	 *
	 * @param $value
	 * @return $this
	 */
	public function forr($value) {
		return $this->addAttribute('for', $value);
	}

}