<?php
/**
 * Em.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <em> element
 *
 * @package Clara\Html\Element
 */
class Em extends Element {

	/**
	 * @var string
	 */
	protected $type = 'em';

	/**
	 * Em elements have no specific attributes beyond the global list
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/em
	 */
	protected $allowedAttributes = array();

} 