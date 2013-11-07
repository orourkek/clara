<?php
/**
 * Img.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\SelfClosingElement;

/**
 * Represents an HTML <img> element
 *
 * @package Clara\Html\Element
 */
class Img extends SelfClosingElement {

	/**
	 * @var string
	 */
	protected $type = 'img';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img
	 */
	protected $allowedAttributes = array(
		'alt',
		'crossorigin',
		'height',
		'hspace',
		'ismap',
		'src',
		'width',
		'usemap',
	);

} 