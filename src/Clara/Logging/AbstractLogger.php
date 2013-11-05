<?php
/**
 * AbstractLogger.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Logging;


abstract class AbstractLogger {

	/**
	 * @param $level
	 * @param $message
	 * @return mixed
	 */
	abstract protected function log($level, $message);

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @return null
	 */
	public function emergency($message)
	{
		$this->log(LogLevel::EMERGENCY, $message);
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @return null
	 */
	public function alert($message)
	{
		$this->log(LogLevel::ALERT, $message);
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @return null
	 */
	public function critical($message)
	{
		$this->log(LogLevel::CRITICAL, $message);
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @return null
	 */
	public function error($message)
	{
		$this->log(LogLevel::ERROR, $message);
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @return null
	 */
	public function warning($message)
	{
		$this->log(LogLevel::WARNING, $message);
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @return null
	 */
	public function notice($message)
	{
		$this->log(LogLevel::NOTICE, $message);
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @return null
	 */
	public function info($message)
	{
		$this->log(LogLevel::INFO, $message);
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @return null
	 */
	public function debug($message)
	{
		$this->log(LogLevel::DEBUG, $message);
	}

} 