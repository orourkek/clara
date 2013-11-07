<?php
/**
 * Footer.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <footer> element
 *
 * @package Clara\Html\Element
 */
class Footer extends Element {

	/**
	 * @var string
	 */
	protected $type = 'footer';

	/**
	 * Footer elements have no specific attributes beyond the global list
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/footer
	 */
	protected $allowedAttributes = array();

}