<?php
/**
 * Article.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html\Element;

use Clara\Html\Element;

/**
 * Represents an HTML <article> element
 *
 * @package Clara\Html\Element
 */
class Article extends Element {

	/**
	 * @var string
	 */
	protected $type = 'article';

	/**
	 * Article elements have no specific attributes beyond the global list
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/article
	 */
	protected $allowedAttributes = array();

} 