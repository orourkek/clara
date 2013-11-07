<?php
/**
 * Input.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\SelfClosingElement;

/**
 * Represents an HTML <input> element
 *
 * @package Clara\Html\Element
 */
class Input extends SelfClosingElement {

	/**
	 * @var string
	 */
	protected $type = 'input';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input
	 */
	protected $allowedAttributes = array(
		'type',
		'accept',
		'accesskey',
		'autocomplete',
		'autofocus',
		'autosave',
		'checked',
		'disabled',
		'form',
		'formaction',
		'formenctype',
		'formmethod',
		'formnovalidate',
		'formtarget',
		'height',
		'inputmode',
		'list',
		'maxlength',
		'min',
		'multiple',
		'name',
		'pattern',
		'placeholder',
		'readonly',
		'required',
		'selectionDirection',
		'size',
		'spellcheck',
		'src',
		'step',
		'value',
		'width',
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
	public function name($value) {
		return $this->addAttribute('name', $value);
	}

	/**
	 * Shortcut method for a common attribute for this element type
	 *
	 * @param $value
	 * @return $this
	 */
	public function value($value) {
		return $this->addAttribute('value', $value);
	}

}