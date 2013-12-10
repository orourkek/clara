<?php
/**
 * ControllerHandler.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Routing;

use Clara\Exception\ClaraInvalidArgumentException;

/**
 * Represents a route handler of the scheme "controller@method"
 *
 * @package Clara\Routing
 */
class ControllerHandler {

	/**
	 * The class to be used
	 *
	 * @var string
	 */
	protected $clazz;

	/**
	 * The class method to be called
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * @param string $clazz
	 * @param string $method
	 * @throws \Clara\Exception\ClaraInvalidArgumentException
	 */
	public function __construct($clazz, $method) {
		if( ! class_exists($clazz)) {
			throw new ClaraInvalidArgumentException(sprintf('ControllerHandler class not found: "%s"', $clazz));
		}
		if( ! method_exists($clazz, $method)) {
			throw new ClaraInvalidArgumentException(sprintf('ControllerHandler method not found: "%s::%s"', $clazz, $method));
		}
		$this->clazz = $clazz;
		$this->method = $method;
	}

	/**
	 * Instantiates an object of the required class, and returns a callable: array($obj, 'method')
	 *
	 * @return callable
	 */
	public function getCallable() {
		return array(new $this->clazz(), $this->method);
	}

} 