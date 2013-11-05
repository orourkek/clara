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

use Clara\Events\Observable;
use Clara\Events\Event;


/**
 * Class AbstractLogger
 *
 * EVENTS WITHIN:
 *  log.emergency
 *  log.alert
 *  log.critical
 *  log.error
 *  log.warning
 *  log.notice
 *  log.info
 *  log.debug
 *
 * @package Clara\Logging
 */
abstract class AbstractLogger extends Observable {

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
		$this->fire(new Event(sprintf('log.%s', LogLevel::EMERGENCY), $this, $message));
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
		$this->fire(new Event(sprintf('log.%s', LogLevel::ALERT), $this, $message));
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
		$this->fire(new Event(sprintf('log.%s', LogLevel::CRITICAL), $this, $message));
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
		$this->fire(new Event(sprintf('log.%s', LogLevel::ERROR), $this, $message));
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
		$this->fire(new Event(sprintf('log.%s', LogLevel::WARNING), $this, $message));
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
		$this->fire(new Event(sprintf('log.%s', LogLevel::NOTICE), $this, $message));
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
		$this->fire(new Event(sprintf('log.%s', LogLevel::INFO), $this, $message));
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
		$this->fire(new Event(sprintf('log.%s', LogLevel::DEBUG), $this, $message));
		$this->log(LogLevel::DEBUG, $message);
	}

} 