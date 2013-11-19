<?php
/**
 * ClaraRuntimeException.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Exception;

use RuntimeException;
use Exception;


/**
 * @package Clara\Exception
 * @codeCoverageIgnore
 */
class ClaraRuntimeException extends RuntimeException {

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