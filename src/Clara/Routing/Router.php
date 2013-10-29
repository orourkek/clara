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
 * Class Router
 *
 * EVENTS WITHIN:
 *  router.route.success
 *  router.route.failure
 *  router.addRoute
 *
 * @package Clara\Routing
 */
class Router extends Observable {

	/**
	 * @var \Clara\Routing\Route[]
	 */
	protected $routes;

	/**
	 * @param Request $request
	 * @return bool
	 */
	public function Route(Request $request) {
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