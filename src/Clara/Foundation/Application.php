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
use Clara\Support\ErrorLogger;
use Exception;

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
		$this->registerErrorHandler();
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
			$this->fire(new Event('application.run.not-found', $this));
			$this->failGracefully('404 Not Found', 'The requested URL was not found on this server', Response::HTTP_NOT_FOUND);
		}
		$this->fire(new Event('application.run.complete', $this, $request));
	}

	/**
	 * Registers a handler for errors and exceptions
	 *
	 * @return $this
	 */
	protected function registerErrorHandler() {
		$handler = new ErrorLogger($this->config['logsDir']);
		$handler->register();
		return $this;
	}

	/**
	 * @param string $title
	 * @param string $message
	 * @param int    $httpStatusCode
	 */
	protected function failGracefully($title, $message='', $httpStatusCode=500) {
		$this->fire(new Event('application.graceful-failure', $this));
		$html = sprintf('<!doctype HTML>
			<html>
			<head>
				<meta charset="utf-8">
				<meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
				<title>Oops! Something went wrong...</title>
				<style>
					* { font-family: "Helvetica Nueue", Helvetica, arial, sans-serif; color: #444; }
					html { background: #eee; }
					main { max-width: 400px; margin: auto; }
					div.clara-error-message {
						max-width: 400px;
						margin: 50px auto 0;
						-webkit-box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
						-moz-box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
						box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
						padding: 24px;
						background: #fff;
						border: 1px solid #ccc;
						text-align: center;
					}
					h1 { font-size: 24px; font-weight: bold; }
					h2 { font-size: 12px; font-weight: normal; color: #888; }
				</style>
			</head>
			<body>
				<header></header>
				<main>
					<div class="clara-error-message">
						<h1>%s</h1>
						<h2>%s</h2>
					</div>
				</main>
				<footer></footer>
			</body>
			</html>', $title, $message);
		try {
			$response = new Response($html, $httpStatusCode);
		} catch(Exception $e) {
			$response = new Response($html, Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		$response->send();
		exit;
	}

	/**
	 * Applies loaded configuration values to the application
	 *
	 * @return $this
	 */
	private final function applyConfiguration() {
		if($this->debugMode = $this->config['debug']) {
			$observer = new Logger($this->config['logsDir'], true);
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