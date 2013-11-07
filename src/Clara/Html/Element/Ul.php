<?php
/**
 * Ul.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <ul> element
 *
 * @package Clara\Html\Element
 */
class Ul extends Element {

	/**
	 * @var string
	 */
	protected $type = 'ul';

	/**
	 * Ul elements have no specific attributes beyond the global list
	 *
	 * NOTE: The two attributes formerly allowed ("compact" and "type") are now DEPRECATED. See @link for more info
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/ul
	 */
	protected $allowedAttributes = array();

}