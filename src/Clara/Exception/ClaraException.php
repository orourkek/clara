<?php
/**
 * ClaraException.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Exception;

use \Exception;

/**
 * @package Clara\Exception
 * @codeCoverageIgnore
 */
class ClaraException extends Exception {

	/**
	 * @var Exception
	 */
	public $previous;

	/**
	 * @param Exception $e
	 * @return $this
	 */
	public function setPrevious(Exception $e) {
		$this->previous = $e;
		return $this;
	}

}