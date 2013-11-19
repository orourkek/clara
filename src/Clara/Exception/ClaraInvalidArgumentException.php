<?php
/**
 * ClaraInvalidArgumentException.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Exception;

use Exception;
use InvalidArgumentException;

/**
 * @package Clara\Exception
 * @codeCoverageIgnore
 */
class ClaraInvalidArgumentException extends InvalidArgumentException {

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