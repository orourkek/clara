<?php
/**
 * DatabaseException.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Database\Exception;

use Clara\Exception\ClaraRuntimeException;

/**
 * @package Clara\Database\Exception
 * @codeCoverageIgnore
 */
class DatabaseException extends ClaraRuntimeException {

	/**
	 * @var string
	 */
	public $relevantQuery;

	/**
	 * @param string $relevantQuery
	 * @return $this
	 */
	public function setRelevantQuery($relevantQuery) {
		$this->relevantQuery = $relevantQuery;
		return $this;
	}

} 