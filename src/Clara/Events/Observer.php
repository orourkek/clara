<?php
/**
 * Observer.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Events;


abstract class Observer {

	abstract public function witness(Event $event);

} 