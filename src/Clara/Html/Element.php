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


abstract class Element {

	/**
	 * The element type, e.g. "input", "form", "img", "strong"
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * @var \Clara\Html\Attribute[]
	 */
	protected $attributes = array();

	/**
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
	 * @param mixed $content
	 */
	public function __construct($content=null) {
		if( ! empty($content)) {
			if(is_array($content)) {
				foreach($content as $cont) {
					$this->addContent($cont);
				}
			} else {
				$this->addContent($content);
			}
		}
	}

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
	 * @param $value
	 * @return $this
	 */
	public function style($value) {
		return $this->addAttribute('style', $value);
	}

	/**
	 * Shortcut method for a commonly used attribute
	 *
	 * NOTE: Spelling is because "class" is a reserved word
	 *
	 * @param $value
	 * @return $this
	 */
	public function clazz($value) {
		return $this->addAttribute('class', $value);
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