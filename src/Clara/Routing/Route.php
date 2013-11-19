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

use Clara\Routing\Exception\RoutingException;
use Clara\Http\Request;

/**
 * A route definition
 *
 * @package Clara\Routing
 */
class Route {

	/**
	 * The route name (only used internally for convenience)
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The raw pattern (pseudo-regex). Is compiled to a regex in Route::compileRegex()
	 *
	 * @var string
	 */
	protected $pattern;

	/**
	 * The handler for the Route (callable or ControllerHandler)
	 *
	 * @var mixed
	 */
	protected $handler;

	/**
	 * Array of HTTP methods that this Route should handle
	 *
	 * @var array
	 */
	protected $methods = array('GET');

	/**
	 * Regular expression compiled from Route::$pattern through Route::compileRegex()
	 *
	 * @var string
	 */
	protected $regex;

	/**
	 * Parameters that are pulled from the request URI when Route::matches() is called.
	 * Will be passed to the handler as parameters on Route::run()
	 *
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
			if(is_string($handler) && 1 === preg_match('#^(?P<className>[\\a-zA-Z]+)@(?P<methodName>[a-zA-Z]+)$#', $handler, $matches)) {
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
		$this->pattern = rtrim($pattern, '/');
		$this->regex = $this->compileRegex();
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
	 * Throws exception, because this property is immutable.
	 *
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
	 * If the Route matches the request (method & pattern)
	 * On successful match, parameters are pulled from the URI and put into Route::$parameters
	 *
	 * @param Request $request
	 * @return bool
	 * @usedby \Clara\Routing\Router::route
	 */
	public function matches(Request $request) {
		if( ! $this->canHandleMethod($request->getMethod())) {
			return false;
		}
		$matches = array();
		$result = preg_match($this->regex, $request->getUri()->getRequestUri(), $matches);
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
	 * Runs the route as defined, passing control to the handler
	 *
	 * @return mixed
	 * @usedby \Clara\Foundation\Application::run
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
	 * Compiles the pattern into a regular expression
	 *
	 * @return string
	 * @throws \Clara\Routing\Exception\RoutingException
	 */
	protected final function compileRegex() {
		$pattern = $this->pattern;
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
				$pattern = str_replace('{' . $varName . '}', '(?P<' . $varName . '>[^/]+)', $pattern);
			}
		}

		return sprintf('#^%s/?$#i', $pattern);
	}

	/**
	 * Whether or not the given $input is a valid pattern
	 *
	 * @param $input
	 * @return bool
	 */
	public static function patternIsValid($input) {
		$validCharactersRegex = '#^[/\w{}]*$#';
		return (is_string($input) && (1 === preg_match($validCharactersRegex, $input)));
	}

	/**
	 * Convenience method for constructing a Route object that responds to the GET method
	 *
	 * @param $pattern
	 * @param $handler
	 * @return \Clara\Routing\Route
	 */
	public static function get($pattern, $handler) {
		return new Route('GET', $pattern, $handler);
	}

	/**
	 * Convenience method for constructing a Route object that responds to the POST method
	 *
	 * @param $pattern
	 * @param $handler
	 * @return \Clara\Routing\Route
	 */
	public static function post($pattern, $handler) {
		return new Route('POST', $pattern, $handler);
	}

	/**
	 * Convenience method for constructing a Route object that responds to the PUT method
	 *
	 * @param $pattern
	 * @param $handler
	 * @return \Clara\Routing\Route
	 */
	public static function put($pattern, $handler) {
		return new Route('PUT', $pattern, $handler);
	}

	/**
	 * Convenience method for constructing a Route object that responds to the DELETE method
	 *
	 * @param $pattern
	 * @param $handler
	 * @return \Clara\Routing\Route
	 */
	public static function delete($pattern, $handler) {
		return new Route('DELETE', $pattern, $handler);
	}

}