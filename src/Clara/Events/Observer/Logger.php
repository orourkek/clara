<?php
/**
 * Logger.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Events\Observer;

use Clara\Events\Event;
use Clara\Events\Observer;
use Clara\Logging\Writer;

class Logger extends Observer {

	/**
	 * @var \Clara\Logging\Writer
	 */
	protected $writer;

	/**
	 * @var bool
	 */
	protected $verbose;

	/**
	 * @param      $location
	 * @param bool $verbose
	 */
	public function __construct($location, $verbose=false) {
		$this->writer = new Writer($location);
		$this->verbose = $verbose;
	}

	/**
	 * @param Event $event
	 */
	public function witness(Event $event) {
		$message = $this->constructMessage($event);
		$this->writer->debug($message);
	}

	/**
	 * @param Event $event
	 * @return string
	 */
	protected function constructMessage(Event $event) {
		$message = sprintf('Event "%s" occurred in "%s"', $event->getName(), get_class($event->getSubject()));
		if($this->verbose) {
			ob_start();
			var_dump($event->getContext());
			$message .= sprintf(':%s%s', PHP_EOL, ob_get_clean());
		}
		return $message;
	}
}