<?php
/**
 * Form.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <form> element
 *
 * @package Clara\Html\Element
 */
class Form extends Element {

	/**
	 * @var string
	 */
	protected $type = 'form';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/form
	 */
	protected $allowedAttributes = array(
		'accept-charset',
		'action',
		'autocomplete',
		'enctype',
		'method',
		'name',
		'novalidate',
		'target',
	);

	/**
	 * Shortcut method for a common attribute for this element type
	 *
	 * @param $value
	 * @return $this
	 */
	public function method($value) {
		return $this->addAttribute('method', $value);
	}

	/**
	 * Shortcut method for a common attribute for this element type
	 *
	 * @param $value
	 * @return $this
	 */
	public function action($value) {
		return $this->addAttribute('action', $value);
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

}