<?php
/**
 * Event.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Events;

use DateTime;

/**
 * Class Event
 *
 * @package Clara\Events
 */
class Event {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var mixed
	 */
	protected $subject;

	/**
	 * @var mixed
	 */
	protected $context;

	/**
	 * @var DateTime
	 */
	protected $timestamp;

	/**
	 * @param      $name
	 * @param null $subject
	 * @param null $context
	 */
	public function __construct($name, $subject=null, $context=null) {
		$this->name = $name;
		$this->subject = $subject;
		$this->context = $context;
		$this->timestamp = new DateTime();
	}

	/**
	 * @return mixed|null
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return mixed|null
	 */
	public function getSubject() {
		return $this->subject;
	}

} 