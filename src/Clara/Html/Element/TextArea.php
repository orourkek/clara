<?php
/**
 * TextArea.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <textarea> element
 *
 * @package Clara\Html\Element
 */
class TextArea extends Element {

	/**
	 * @var string
	 */
	protected $type = 'textarea';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea
	 */
	protected $allowedAttributes = array(
		'autofocus',
		'cols',
		'disabled',
		'form',
		'maxlength',
		'name',
		'placeholder',
		'readonly',
		'required',
		'rows',
		'selectiondirection',
		'selectionend',
		'selectionstart',
		'spellcheck',
		'wrap',
	);

}