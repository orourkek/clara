<?php
/**
 * Div.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <div> element
 *
 * @package Clara\Html\Element
 */
class Div extends Element {

	/**
	 * @var string
	 */
	protected $type = 'div';

	/**
	 * Div elements have no specific attributes beyond the global list
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/div
	 */
	protected $allowedAttributes = array();

} 