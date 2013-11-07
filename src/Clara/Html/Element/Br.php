<?php
/**
 * Br.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\SelfClosingElement;

/**
 * Represents an HTML <br> element
 *
 * @package Clara\Html\Element
 */
class Br extends SelfClosingElement {

	/**
	 * @var string
	 */
	protected $type = 'br';

	/**
	 * Br elements have no attributes. The only attribute ("clear") was deprecated with HTML 4.0.1
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/br
	 */
	protected $allowedAttributes = array();

} 