<?php
/**
 * Li.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <li> element
 *
 * @package Clara\Html\Element
 */
class Li extends Element {

	/**
	 * @var string
	 */
	protected $type = 'li';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/li
	 */
	protected $allowedAttributes = array(
		'value',
		'type', //"deprecated, but should still work"
	);

} 