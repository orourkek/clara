<?php
/**
 * Fieldset.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <fieldset> element
 *
 * @package Clara\Html\Element
 */
class Fieldset extends Element {

	/**
	 * @var string
	 */
	protected $type = 'fieldset';

	/**
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/fieldset
	 */
	protected $allowedAttributes = array(
		'disabled',
		'form',
		'name',
	);

} 