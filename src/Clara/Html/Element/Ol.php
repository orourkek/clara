<?php
/**
 * Ol.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <ol> element
 *
 * @package Clara\Html\Element
 */
class Ol extends Element {

	/**
	 * @var string
	 */
	protected $type = 'ol';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/ol
	 */
	protected $allowedAttributes = array(
		'reversed',
		'start',
		'type',
	);

}