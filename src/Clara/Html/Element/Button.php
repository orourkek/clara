<?php
/**
 * Button.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <button> element
 *
 * @package Clara\Html\Element
 */
class Button extends Element {

	/**
	 * @var string
	 */
	protected $type = 'button';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/button
	 */
	protected $allowedAttributes = array(
		'autofocus',
		'disabled',
		'form',
		'formaction',
		'formenctype',
		'formmethod',
		'formonvalidate',
		'formtarget',
		'name',
		'type',
		'value',
	);


	/**
	 * Shortcut method for a common attribute for this element type
	 *
	 * @param $value
	 * @return $this
	 */
	public function name($value) {
		return $this->addAttribute('name', $value);
	}

	/**
	 * Shortcut method for a common attribute for this element type
	 *
	 * @param $value
	 * @return $this
	 */
	public function type($value) {
		return $this->addAttribute('type', $value);
	}
} 