<?php
/**
 * Observable.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Events;


/**
 * Class Observable
 *
 * @package Clara\Events
 */
abstract class Observable {

	/**
	 * @var \Clara\Events\Observer[]
	 */
	protected $observers = array();

	/**
	 * @param Observer $observer
	 * @return $this
	 */
	public function attach(Observer $observer) {
		$this->observers[] = $observer;
		return $this;
	}

	/**
	 * @param Observer $observer
	 * @return $this
	 */
	public function detatch(Observer $observer) {
		$key = array_search($observer, $this->observers, true);
		if(false !== $key) {
			unset($this->observers[$key]);
		}
		return $this;
	}

	/**
	 * @param Event $event
	 * @return $this
	 */
	public function fire(Event $event) {
		foreach ($this->observers as $observer) {
			$observer->witness($event);
		}
		return $this;
	}

} 