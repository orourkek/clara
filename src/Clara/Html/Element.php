<?php
/**
 * Element.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Html;

use Clara\Exception\ClaraDomainException;
use Clara\Support\Contract\Stringable;

/**
 * Represents an abstract HTML element
 *
 * @package Clara\Html
 */
abstract class Element implements Stringable {

	/**
	 * The element type, e.g. "input", "form", "img", "strong"
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * Array of element attributes (\Clara\Html\Attribute)
	 *
	 * @var \Clara\Html\Attribute[]
	 */
	protected $attributes = array();

	/**
	 * Array of element content (order is preserved on string compilation)
	 *
	 * @var string|\Clara\Html\Element[]
	 */
	protected $content = array();

	/**
	 * Element-specific attributes (to be defined in classes that extend Element)
	 *
	 * @var string[]
	 */
	protected $allowedAttributes = array();

	/**
	 * Globally allowed attributes. See @link for mroe info
	 *
	 * @var string[]
	 * @link https://developer.mozilla.org/en-US/docs/HTML/Global_attributes
	 */
	protected static $globalAttributes = array(
		'accesskey',
		'class',
		'contenteditable',
		'contextmenu',
		'data-*', //special case, hardcoded in isValidAttribute
		'dir',
		'draggable',
		'hidden',
		'id',
		'itemid',
		'itemprop',
		'itemref',
		'itemscope',
		'itemtype',
		'lang',
		'spellcheck',
		'style',
		'tabindex',
		'title',
	);

	/**
	 * @param string $attribute
	 * @param mixed  $value
	 * @return $this
	 * @throws \Clara\Exception\ClaraDomainException
	 */
	public function addAttribute($attribute, $value) {
		if( ! $this->isValidAttribute($attribute)) {
			throw new ClaraDomainException(sprintf('Invalid HTML element attribute "%s"', $attribute));
		}
		$this->attributes[$attribute] = new Attribute($attribute, $value);
		return $this;
	}

	/**
	 * Adds an array of attribute=>value pairs to the element
	 *
	 * @param array $attributes
	 * @return $this
	 */
	public function addAttributes($attributes) {
		foreach($attributes as $attribute => $value) {
			$this->addAttribute($attribute, $value);
		}
		return $this;
	}

	/**
	 * Appends a value to the given attribute
	 *
	 * @param string $attribute
	 * @param mixed  $value
	 * @param bool   $overwrite
	 * @return $this
	 */
	public function appendAttribute($attribute, $value, $overwrite=false) {
		if( ! $this->isValidAttribute($attribute)) {
			throw new ClaraDomainException(sprintf('Invalid HTML element attribute "%s"', $attribute));
		}
		if($overwrite || ! $this->hasAttribute($attribute)) {
			return $this->addAttribute($attribute, $value);
		}
		$this->attributes[$attribute]->append($value);
		return $this;
	}

	/**
	 * Appends an array of attribute=>value pairs to the element
	 *
	 * @param array $attributes
	 * @param bool  $overwrite
	 * @return $this
	 */
	public function appendAttributes($attributes, $overwrite=false) {
		foreach($attributes as $attribute => $value) {
			$this->appendAttribute($attribute, $value, $overwrite);
		}
		return $this;
	}

	/**
	 * @param $attribute
	 * @return bool
	 */
	public function hasAttribute($attribute) {
		return array_key_exists($attribute, $this->attributes);
	}

	/**
	 * @param string|\Clara\Html\Element $content
	 * @return $this
	 * @throws \Clara\Exception\ClaraDomainException
	 * @throws \Clara\Html\Exception\HtmlLogicException
	 */
	public function addContent($content) {
		if( ! is_string($content) && ! $content instanceof Element) {
			throw new ClaraDomainException('Element inner content must be either a string or \Clara\Html\Element');
		}
		$this->content[] = $content;
		return $this;
	}

	/**
	 * Returns the opening tag of the element as a string
	 *
	 * @return string
	 */
	public function open() {
		return sprintf('<%s%s%s>', $this->type, (empty($this->attributes) ? '' : ' '), implode(' ', $this->attributes));
	}

	/**
	 * Returns the content of the element as a string
	 *
	 * @return string
	 */
	public function content() {
		return implode('', $this->content);
	}

	/**
	 * Returns the closing tag of the element as a string
	 *
	 * @return string
	 */
	public function close() {
		return sprintf('</%s>', $this->type);
	}

	/**
	 * Shortcut method for a commonly used attribute
	 *
	 * @param $value
	 * @return $this
	 */
	public function id($value) {
		return $this->addAttribute('id', $value);
	}

	/**
	 * Shortcut method for a commonly used attribute
	 *
	 * @param      $value
	 * @param bool $overwrite
	 * @return $this
	 */
	public function style($value, $overwrite=false) {
		return $this->appendAttribute('style', $value, $overwrite);
	}

	/**
	 * Shortcut method for a commonly used attribute
	 *
	 * NOTE: Spelling is because "class" is a reserved word
	 *
	 * @param      $value
	 * @param bool $overwrite
	 * @return $this
	 */
	public function clazz($value, $overwrite=false) {
		return $this->appendAttribute('class', $value, $overwrite);
	}

	/**
	 * Returns the entire element as a string
	 *
	 * @return string
	 */
	public function __toString() {
		$str = '';
		$str .= $this->open();
		$str .= $this->content();
		$str .= $this->close();
		return $str;
	}

	/**
	 * If the attribute is valid (appears in either globalAttributes or allowedAttributes)
	 *
	 * @param string $attribute
	 * @return bool
	 */
	protected function isValidAttribute($attribute) {
		if(is_string($attribute)) {
			if(in_array($attribute, static::$globalAttributes)
				|| in_array($attribute, $this->allowedAttributes)
				|| $this->isValidWildcardAttribute($attribute)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Special case for validating wildcard attributes (if any have been assigned), e.g. "data-*"
	 *
	 * @param string $attribute
	 * @return bool
	 */
	protected function isValidWildcardAttribute($attribute) {
		foreach(array_merge(static::$globalAttributes, $this->allowedAttributes) as $attr) {
			if(false !== strpos($attr, '*')) {
				$pattern = sprintf('#^%s$#', str_replace('*', '(.+)', $attr));
				if(1 === preg_match($pattern, $attribute)) {
					return true;
				}
			}
		}
		return false;
	}
}