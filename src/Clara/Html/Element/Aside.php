<?php
/**
 * Aside.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <aside> element
 *
 * @package Clara\Html\Element
 */
class Aside extends Element {

	/**
	 * @var string
	 */
	protected $type = 'aside';

	/**
	 * Aside elements have no specific attributes beyond the global list
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/aside
	 */
	protected $allowedAttributes = array();

} 