<?php
/**
 * ErrorHandler.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara\Support
 */

namespace Clara\Support;

use Exception;

/**
 * Class Handler
 * @package Clara\Exception
 */
abstract class ErrorHandler {

	/**
	 * @var array
	 */
	protected $errorLevels = array(
		E_ALL				=> 'E_ALL',
		E_USER_NOTICE		=> 'E_USER_NOTICE',
		E_USER_WARNING		=> 'E_USER_WARNING',
		E_USER_ERROR		=> 'E_USER_ERROR',
		E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
		E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
		E_CORE_WARNING		=> 'E_CORE_WARNING',
		E_CORE_ERROR		=> 'E_CORE_ERROR',
		E_NOTICE			=> 'E_NOTICE',
		E_PARSE				=> 'E_PARSE',
		E_WARNING			=> 'E_WARNING',
		E_ERROR				=> 'E_ERROR',
	);

	/**
	 * Registers itself as error and exception handler
	 */
	public function register() {
		$this->registerErrorHandler();
		$this->registerExceptionHandler();
	}

	/**
	 * @return $this
	 */
	protected function registerErrorHandler() {
		set_error_handler(array($this, 'handleError'));
		return $this;
	}

	/**
	 * @return $this
	 */
	protected function registerExceptionHandler() {
		set_exception_handler(array($this, 'handleException'));
		return $this;
	}

	/**
	 * Fetches a string to represent the supplied PHp error level
	 *
	 * @param $code
	 * @return string
	 */
	protected function errorName($code) {
		return array_key_exists($code, $this->errorLevels)? $this->errorLevels[$code] : "UNKNOWN";
	}

	/**
	 * @param $level
	 * @param $message
	 * @param $file
	 * @param $line
	 * @param $context
	 * @return mixed
	 */
	abstract public function handleError($level, $message, $file, $line, $context);

	/**
	 * @param \Exception $exception
	 * @return mixed
	 */
	abstract public function handleException(Exception $exception);

}