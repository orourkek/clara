<?php
/**
 * Nav.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <nav> element
 *
 * @package Clara\Html\Element
 */
class Nav extends Element {

	/**
	 * @var string
	 */
	protected $type = 'nav';

	/**
	 * Nav elements have no specific attributes beyond the global list
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/nav
	 */
	protected $allowedAttributes = array();

}