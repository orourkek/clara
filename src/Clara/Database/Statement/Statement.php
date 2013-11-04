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

/**
 * Class Statement
 *
 * @package Clara\Database\Statement
 */
abstract class Statement {

	/**
	 * @return string
	 */
	abstract public function __toString();

	/**
	 * @return string
	 */
	public function toStringAsSubQuery() {
		return sprintf('(%s)', (string) $this);
	}

}