<?php
/**
 * Style.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <style> element
 *
 * @package Clara\Html\Element
 */
class Style extends Element {

	/**
	 * @var string
	 */
	protected $type = 'style';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/style
	 */
	protected $allowedAttributes = array(
		'type',
		'media',
		'scoped',
		'title',
		'disabled',
	);

} 