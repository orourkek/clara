<?php
/**
 * SelfClosingElement.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html;

use Clara\Html\Exception\HtmlLogicException;
use Clara\Support\Contract\Stringable;

/**
 * Represents an abstract self-closing HTML element, e.g. <img ... />
 *
 * @package Clara\Html
 */
abstract class SelfClosingElement extends Element implements Stringable {

	/**
	 * Constructor deliberately does nothing.
	 *
	 * The constructor for Element accepts content to be added.
	 * Since SC elements have no content, this method is overridden to do nothing.
	 */
	public function __construct() {}

	/**
	 * Self closing elements cannot have content, so this method is overridden to throw an HtmlLogicException
	 *
	 * @param Element|string $content
	 * @return $this|void
	 * @throws Exception\HtmlLogicException
	 */
	public function addContent($content) {
		throw new HtmlLogicException('Self-closing elements cannot have inner content');
	}

	/**
	 * Returns the opening tag of the element as a string
	 *
	 * @return string
	 */
	public function open() {
		return sprintf('<%s%s%s/>', $this->type, (empty($this->attributes) ? '' : ' '), implode(' ', $this->attributes));
	}

	/**
	 * Returns the content of the element (for self closing elements this is always an empty string)
	 *
	 * @return string
	 */
	public function content() {
		return '';
	}

	/**
	 * Returns the closing tag of the element (for self closing elements this is always an empty string)
	 *
	 * @return string
	 */
	public function close() {
		return '';
	}


}