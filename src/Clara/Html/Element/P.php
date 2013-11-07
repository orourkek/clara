<?php
/**
 * P.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <p> element
 *
 * @package Clara\Html\Element
 */
class P extends Element {

	/**
	 * @var string
	 */
	protected $type = 'p';

	/**
	 * P elements have no specific attributes beyond the global list
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/p
	 */
	protected $allowedAttributes = array();

} 