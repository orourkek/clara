<?php
/**
 * Statement.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Database\Statement;
use Clara\Support\Contract\Stringable;

/**
 * Represents an abstract MySQL statement
 *
 * @package Clara\Database\Statement
 */
abstract class Statement implements Stringable {

	/**
	 * @return string
	 */
	abstract public function __toString();

	/**
	 * Returns self as a subquery string in parentheses
	 *
	 * @return string
	 */
	public function toStringAsSubQuery() {
		return sprintf('(%s)', (string) $this);
	}

}