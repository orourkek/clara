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


class DatabaseException extends ClaraRuntimeException {

	/**
	 * @var string
	 */
	public $relevantQuery;

	/**
	 * @param string $relevantQuery
	 */
	public function setRelevantQuery($relevantQuery) {
		$this->relevantQuery = $relevantQuery;
		return $this;
	}

} 