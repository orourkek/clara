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


abstract class SelfClosingElement extends Element {

	/**
	 * @param Element|string $content
	 * @return $this|void
	 * @throws Exception\HtmlLogicException
	 */
	public function addContent($content) {
		throw new HtmlLogicException('Self-closing elements cannot have inner content');
	}

	/**
	 * @return string
	 */
	public function open() {
		return sprintf('<%s%s%s/>', $this->type, (empty($this->attributes) ? '' : ' '), implode(' ', $this->attributes));
	}

	/**
	 * @return string
	 */
	public function content() {
		return '';
	}

	/**
	 * @return string
	 */
	public function close() {
		return '';
	}


}