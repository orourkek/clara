<?php
/**
 * LogLevel.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Logging;

/**
 * Log severity levels, as defined in RFC5424. See @link for more info
 *
 * @package Clara\Logging
 * @link http://tools.ietf.org/html/rfc5424#page-11
 */
class LogLevel {

	/**
	 * System is unusable
	 */
	const EMERGENCY = 'emergency';

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should trigger alerts and wake developers up.
	 */
	const ALERT = 'alert';

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 */
	const CRITICAL = 'critical';

	/**
	 * Runtime errors that do not require immediate action but should typically be logged and monitored.
	 */
	const ERROR = 'error';

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
	 */
	const WARNING = 'warning';

	/**
	 * Normal but significant events.
	 */
	const NOTICE = 'notice';

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 */
	const INFO = 'info';

	/**
	 * Detailed debug information.
	 */
	const DEBUG = 'debug';

} 