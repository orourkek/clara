<?php
/**
 * ErrorLogger.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Support;

use Clara\Exception\ErrorHandler;
use Clara\Logging\Writer;
use Exception;

/**
 * An error/exception handler that logs info to log files
 *
 * @package Clara\Support
 */
class ErrorLogger extends ErrorHandler {

	/**
	 * @var \Clara\Logging\Writer
	 */
	protected $writer;

	/**
	 * @param $logsLocation
	 */
	public function __construct($logsLocation) {
		$this->writer = new Writer($logsLocation);
	}

	/**
	 * @param $level
	 * @param $message
	 * @param $file
	 * @param $line
	 * @param $context
	 * @return mixed
	 */
	public function handleError($level, $message, $file=null, $line=null, $context=null) {
		$message = sprintf('[PHP:%s] %s in %s @%s', $this->errorName($level), $message, $file, $line);

		switch ($level) {
			case E_USER_ERROR:
			case E_USER_WARNING:
			case E_USER_NOTICE:
			case E_USER_DEPRECATED:
			case E_PARSE:
				$this->writer->info($message);
				break;

			case E_NOTICE:
				//ignore notices...
				break;

			case E_DEPRECATED:
				$this->writer->notice($message);
				break;

			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
				$this->writer->warning($message);
				break;

			case E_ERROR:
				$this->writer->error($message);
				exit(1);
				break;

			case E_CORE_ERROR:
				$this->writer->alert($message);
				exit(2);
				break;

			default:
				$this->writer->debug($message);
				break;
		}
		return true;
	}

	/**
	 * @param \Exception $exception
	 * @return mixed
	 */
	public function handleException(Exception $exception) {
		ob_start();
		var_dump($exception);
		$this->writer->critical(sprintf('Exception Encountered:%s%s', PHP_EOL, ob_get_clean()));
		exit(3);
	}
}