<?php
/**
 * Application.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Foundation;

use Clara\Events\Observable;
use Clara\Events\Event;
use Clara\Events\Observer\Logger;
use Clara\Http\Request;
use Clara\Http\Response;
use Clara\Routing\Exception\RoutingException;
use Clara\Routing\Router;

/**
 * Class Application
 *
 * @package Clara\Foundation
 */
class Application extends Observable {

	/**
	 * @var bool
	 */
	protected $debugMode = false;

	/**
	 * @var \Clara\Routing\Router
	 */
	protected $router;

	/**
	 * @var \Clara\Foundation\ApplicationConfig
	 */
	protected $config;

	/**
	 * @param \Clara\Foundation\ApplicationConfig $config
	 */
	public function __construct(ApplicationConfig $config) {
		$this->config = $config;
		$this->router = new Router();
		$this->applyConfiguration();
		$this->fire(new Event('application.created', $this));
	}

	/**
	 * @param \Clara\Http\Request $request
	 * @uses \Clara\Routing\Route::run
	 */
	public function run(Request $request) {
		$this->fire(new Event('application.run', $this, $request));
		if($matchedRoute = $this->router->route($request)) {
			$this->fire(new Event('application.run.routed', $this, $matchedRoute));
			$response = $matchedRoute->run();
			$this->fire(new Event('application.run.response', $this, $response));
			$response->send();
		} else {
			$this->fire(new Event('application.run.not-found', $this, $response));
			$response = new Response('404 Not Found', Response::HTTP_NOT_FOUND);
			$response->send();
		}
		$this->fire(new Event('application.run.complete', $this, $request));
	}

	/**
	 * Applies loaded configuration values to the application
	 *
	 * @return $this
	 */
	private function applyConfiguration() {
		if($this->debugMode = $this->config['debug']) {
			$observer = new Logger($this->config['logsDir']);
			$this->attach($observer);
			$this->router->attach($observer);
			$this->fire(new Event('application.debug-on', $this));
		}

		try {
			$this->router->importRoutesFromFile($this->config['routesFile']);
			$this->fire(new Event('application.routes-loaded', $this, $this->config['routesFile']));
		} catch(RoutingException $e) {}

		return $this;
	}

}