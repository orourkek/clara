<?php
/**
 * Identifier.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Database\Statement;

use Clara\Database\Statement\Exception\StatementException;


/**
 * This class represents a MySQL identifier (table name, column name, etc).
 * See @link for information on restrictions and best practices
 *
 * @package Clara\Database\Statement
 * @link http://dev.mysql.com/doc/refman/5.5/en/identifiers.html
 */
class Identifier {

	/**
	 * @var
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 * @var string
	 */
	protected $alias;

	/**
	 * @param        $name
	 * @param string $alias
	 * @param string $prefix
	 */
	public function __construct($name, $alias='', $prefix='') {
		self::validate($name);
		$this->name = $name;

		if( ! empty($alias)) {
			self::validate($alias);
		}
		$this->alias = $alias;

		if( ! empty($prefix)) {
			self::validate($prefix, true);
		}
		$this->prefix = $prefix;
	}

	/**
	 * @param string $alias
	 * @return $this
	 */
	public function setAlias($alias) {
		$this->alias = $alias;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAlias() {
		return $this->alias;
	}

	/**
	 * @param mixed $name
	 * @return $this
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $prefix
	 * @return $this
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @param      $str
	 * @param bool $isAlias
	 * @return bool
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 */
	public static function validate($str, $isAlias=false) {
		if(self::isValid($str, $isAlias)) {
			return true;
		}
		throw new StatementException('Invalid mysql identifier provided. Expecting: string. Received: ' . gettype($str) . '.');

	}

	/**
	 * @return bool
	 */
	public static function isValid($str, $isAlias) {
		if( ! empty($str) && is_string($str) && preg_match('#^[0-9,a-z,A-Z$_]+$#', $str)) {
			$maxLength = ($isAlias) ? 256 : 64;
			if(strlen($str) <= $maxLength) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param $str
	 * @return Identifier
	 */
	public static function fromString($str) {
		if( ! is_string($str)) {
			throw new StatementException(__METHOD__ . ' requires a string argument. Received: ' . gettype($str) . '.');
		}

		$alias = '';
		$prefix = '';

		if(1 === preg_match('#^`?([0-9,a-z,A-Z$_]+?)`?\.`?([0-9,a-z,A-Z$_]+?)`? AS `?([0-9,a-z,A-Z$_]+?)`?$#i', $str, $matches)) {
			$prefix = $matches[1];
			$name = $matches[2];
			$alias = $matches[3];
		} else if(1 === preg_match('#^`?([0-9,a-z,A-Z$_]+?)`?\.`?([0-9,a-z,A-Z$_]+?)`?$#', $str, $matches)) {
			$prefix = $matches[1];
			$name = $matches[2];
		} else if(1 === preg_match('#^`?([0-9,a-z,A-Z$_]+?)`? AS `?([0-9,a-z,A-Z$_]+?)`?$#i', $str, $matches)) {
			$name = $matches[1];
			$alias = $matches[2];
		} else {
			$name = $str;
		}
		return new Identifier($name, $alias, $prefix);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$str = '';
		if($this->prefix) {
			$str .= sprintf('`%s`.', $this->prefix);
		}
		$str .= sprintf('`%s`', $this->name);
		if($this->alias) {
			$str .= sprintf(' AS `%s`', $this->alias);
		}
		return $str;
	}

} 