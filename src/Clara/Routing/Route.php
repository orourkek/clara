<?php
/**
 * Route.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Routing;

use Clara\Routing\Exception\RoutingException,
	Clara\Http\Request;


/**
 * Class Route
 *
 * @package Clara\Routing
 */
class Route {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $pattern;

	/**
	 * @var mixed
	 */
	protected $handler;

	/**
	 * @var array
	 */
	protected $methods = array('GET');

	/**
	 * @var string
	 */
	protected $regex;

	/**
	 * @var array
	 */
	protected $parameters = array();


	/**
	 * @param $methods
	 * @param $pattern
	 * @param $handler
	 */
	public function __construct($methods, $pattern, $handler) {
		$this->setMethods($methods);
		$this->setPattern($pattern);
		$this->setHandler($handler);
	}

	/**
	 * Assigns a handler to the route.
	 *
	 * The handler MUST be callable (true === is_callable($handler)). Examples of valid handlers:
	 *      - function() { ... }                [closure]
	 *      - 'className::staticMethod'         [string]
	 *      - array($object, 'methodName')      [array]
	 *
	 * NOTE: The format/scheme 'className@methodName' is PLANNED, BUT NOT YET IMPLEMENTED.
	 * @todo Implement the handler scheme 'className@methodName'
	 *
	 * @link http://php.net/manual/en/language.types.callable.php
	 *
	 * @param $handler
	 *
	 * @return $this
	 * @throws Exception\RoutingException
	 */
	public function setHandler($handler) {
		if( ! is_callable($handler)) {
			if(is_string($handler) && 1 === preg_match('#^(?P<className>[a-zA-Z]+)@(?P<methodName>[a-zA-Z]+)$#', $handler, $matches)) {
				$handler = new ControllerHandler($matches['className'], $matches['methodName']);
			} else {
				throw new RoutingException('Route handler must be callable OR follow guidelines outlined within Clara\Routing\Route');
			}
		}
		$this->handler = $handler;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getHandler() {
		return $this->handler;
	}

	/**
	 * @param $name
	 * @return $this
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param $pattern
	 * @return $this
	 * @throws Exception\RoutingException
	 */
	public function setPattern($pattern) {
		if( ! self::patternIsValid($pattern)) {
			$e = new RoutingException('Invalid pattern given to ' . __METHOD__);
			$e->setRelevantRoute($this);
			throw $e;
		}
		$this->pattern = $pattern;
		$this->regex = $this->compileRegex($pattern);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPattern() {
		return $this->pattern;
	}

	/**
	 * @param $methods
	 */
	public function setMethods($methods) {
		$methods = (array) $methods;
		$this->methods = array_map('strtoupper', $methods);
	}

	/**
	 * @return array
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * @throws \Clara\Routing\Exception\RoutingException
	 */
	public function setRegex() {
		throw new RoutingException('The "regex" property is immutable. See Clara\Routing\Route::setPattern');
	}

	/**
	 * @return string
	 */
	public function getRegex() {
		return $this->regex;
	}

	/**
	 * @return array
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * @return array
	 */
	public function getParameterKeys() {
		return array_keys($this->parameters);
	}

	/**
	 * @return array
	 */
	public function getParameterValues() {
		return array_values($this->parameters);
	}

	/**
	 * @param $method
	 * @return bool
	 */
	public function canHandleMethod($method) {
		return in_array(strtoupper($method), $this->methods);
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function matches(Request $request) {
		if( ! $this->canHandleMethod($request->getMethod())) {
			return false;
		}
		$matches = array();
		$result = preg_match($this->regex, (string) $request->getUri(), $matches);
		if( ! empty($matches)) {
			//get rid of the full pattern match
			if(array_shift($matches) && ! empty($matches)) {
				//at this point, there ARE variables in the uri, so lets pull them out.
				foreach($matches as $key => $value) {
					//is_string is used because all parameters (e.g. {uid}) are replaced with NAMED SUBPATTERNS
					if(is_string($key)) {
						$this->parameters[$key] = $value;
					}
				}
			}

		}
		return (1 === $result) ? true : false;
	}

	/**
	 * Runs the route as defined
	 *
	 * @return mixed
	 */
	public function run() {
		if($this->handler instanceof ControllerHandler) {
			$callable = $this->handler->getCallable();
		} else {
			$callable = $this->handler;
		}
		return call_user_func_array($callable, $this->parameters);
	}

	/**
	 * @param $pattern
	 * @return string
	 * @throws \Clara\Routing\Exception\RoutingException
	 */
	protected final function compileRegex($pattern) {
		//check if there are any variables in the pattern
		if(preg_match_all('#{([a-zA-Z0-9]+)}#', $pattern, $matches, PREG_SET_ORDER)) {
			$paramsToBeReplaced = array();
			foreach($matches as $variable) {
				$varName = $variable[1];
				if(in_array($varName, $paramsToBeReplaced)) {
					$e = new RoutingException('Duplicate variable names are not allowed in route patterns');
					$e->setRelevantRoute($this);
					throw $e;
				}
				$paramsToBeReplaced[] = $varName;
			}
		}

		if( ! empty($paramsToBeReplaced)) {
			foreach($paramsToBeReplaced as $varName) {
				$pattern = str_replace('{' . $varName . '}', '(?P<' . $varName . '>.+)', $pattern);
			}
		}

		return sprintf('#^%s$#i', $pattern);
	}

	/**
	 * @param $input
	 * @return bool
	 */
	public static function patternIsValid($input) {
		//needs to be a string
		if( ! is_string($input)) {
			return false;
		}

		//ensure the input contains only valid characters
		$validCharactersRegex = '#^[/\w{}]*$#';
		if(1 !== preg_match($validCharactersRegex, $input)) {
			return false;
		}

		return true;
	}

	/**
	 * @param $pattern
	 * @param $handler
	 * @return \Clara\Routing\Route
	 */
	public static function get($pattern, $handler) {
		return new Route('GET', $pattern, $handler);
	}

	/**
	 * @param $pattern
	 * @param $handler
	 * @return \Clara\Routing\Route
	 */
	public static function post($pattern, $handler) {
		return new Route('POST', $pattern, $handler);
	}

	/**
	 * @param $pattern
	 * @param $handler
	 * @return \Clara\Routing\Route
	 */
	public static function put($pattern, $handler) {
		return new Route('PUT', $pattern, $handler);
	}

	/**
	 * @param $pattern
	 * @param $handler
	 * @return \Clara\Routing\Route
	 */
	public static function delete($pattern, $handler) {
		return new Route('DELETE', $pattern, $handler);
	}

}