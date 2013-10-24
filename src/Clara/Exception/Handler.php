<?php
/**
 * Handler.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Exception;


class Handler {

	public function register() {
		$this->registerErrorHandler();
		$this->registerExceptionHandler();
	}

	protected function registerErrorHandler() {
		set_error_handler(array($this, 'handleError'));
	}

	protected function registerExceptionHandler() {
		set_exception_handler(array($this, 'handleException'));
	}

	public function handleError($level, $message, $file, $line, $context) {

	}

	public function handleException($exception) {
		throw $exception;
	}

}