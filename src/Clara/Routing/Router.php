<?php
/**
 * Router.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Routing;

use Clara\Events\Event;
use Clara\Events\Observable;
use Clara\Http\Request;
use Clara\Routing\Exception\RoutingException;

/**
 * A Router that can route Requests
 *
 * EVENTS WITHIN:
 *  router.route
 *  router.route.success
 *  router.route.failure
 *  router.addRoute
 *
 * @package Clara\Routing
 */
class Router extends Observable {

	/**
	 * Array of Routes assigned to this router
	 *
	 * @var \Clara\Routing\Route[]
	 */
	protected $routes;

	/**
	 * Matches a request to a Route
	 *
	 * @param Request $request
	 * @return \Clara\Routing\Route|bool
	 * @uses \Clara\Routing\Route::matches
	 */
	public function route(Request $request) {
		$this->fire(new Event('router.route', $this, $request));
		if( empty($this->routes)) {
			trigger_error('Router::route called with no routes loaded', E_USER_WARNING);
		} else {
			foreach($this->routes as $route) {
				if($route->matches($request)) {
					$this->fire(new Event('router.route.success', $this, $route));
					return $route;
				}
			}
		}
		$this->fire(new Event('router.route.failure', $this));
		return false;
	}

	/**
	 * Adds a Route to the Router
	 *
	 * @param $route
	 * @return $this
	 * @throws \Clara\Routing\Exception\RoutingException
	 */
	public function addRoute($route) {
		if( ! $route instanceof Route) {
			$type = (is_object($route)) ? get_class($route) : gettype($route);
			throw new RoutingException('Invalid route given to addRoute(). Expected:"Clara\Routing\Route" Actual:"' . $type . '"');
		}
		$this->fire(new Event('router.addRoute', $this, $route));
		$this->routes[] = $route;
		return $this;
	}

	/**
	 * Imports routes to the Router from a properly formatted routes file
	 *
	 * @param $location
	 * @return $this
	 * @throws \Clara\Routing\Exception\RoutingException
	 */
	public function importRoutesFromFile($location) {
		if( ! is_readable($location)) {
			throw new RoutingException('Routes file not readable: "' . $location . '"');
		}
		$routes = include $location;
		foreach($routes as $route) {
			try {
				$this->addRoute($route);
			} catch(RoutingException $e) {
				trigger_error('Invalid route in file ' . $location . ' - array elements must be of type Clara\Routing\Route', E_USER_ERROR);
			}
		}
		return $this;
	}

}