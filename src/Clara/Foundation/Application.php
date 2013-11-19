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
use Clara\Logging\Writer;
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
	 * @var \Clara\Logging\Writer
	 */
	protected $logger;

	/**
	 * @var array
	 */
	protected $errorLevels = array(
		E_ALL				=> 'E_ALL',
		E_USER_NOTICE		=> 'E_USER_NOTICE',
		E_USER_WARNING		=> 'E_USER_WARNING',
		E_USER_ERROR		=> 'E_USER_ERROR',
		E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
		E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
		E_CORE_WARNING		=> 'E_CORE_WARNING',
		E_CORE_ERROR		=> 'E_CORE_ERROR',
		E_NOTICE			=> 'E_NOTICE',
		E_PARSE				=> 'E_PARSE',
		E_WARNING			=> 'E_WARNING',
		E_ERROR				=> 'E_ERROR',
	);

	/**
	 * @param \Clara\Foundation\ApplicationConfig $config
	 */
	public function __construct(ApplicationConfig $config) {
		$this->config = $config;
		$this->registerErrorHandlers();
		$this->router = new Router();
		$this->logger = new Writer($this->config['logsDir']);
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
			$this->failGracefully(
				'404 Not Found',
				sprintf('The requested URL %s was not found on this server', $request->getUri()->getRequestUri()),
				Response::HTTP_NOT_FOUND,
				'HTTP_NOT_FOUND'
			);
		}
		$this->fire(new Event('application.run.complete', $this, $request));
	}

	/**
	 * @param $level
	 * @param $message
	 * @param $file
	 * @param $line
	 * @param $context
	 * @return mixed
	 */
	public function handleError($level, $message, $file, $line, $context) {
		$message = sprintf('[PHP:%s] %s in %s @%s', $this->getErrorName($level), $message, $file, $line);

		switch ($level) {
			case E_USER_ERROR:
			case E_USER_WARNING:
			case E_USER_NOTICE:
			case E_USER_DEPRECATED:
			case E_PARSE:
				$this->logger->info($message);
				break;

			case E_NOTICE:
				//ignore notices
				break;

			case E_DEPRECATED:
				$this->logger->notice($message);
				break;

			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
				$this->logger->warning($message);
				break;

			case E_ERROR:
				$this->logger->error($message);
				$this->failGracefully(
					'Oops! There was a problem serving the requested page.',
					'We\'ve been notified of the problem and will work quickly to fix it!',
					Response::HTTP_INTERNAL_SERVER_ERROR
				);
				break;

			case E_CORE_ERROR:
				$this->logger->alert($message);
				$this->failGracefully(
					'Oops! There was a problem serving the requested page.',
					'We\'ve been notified of the problem and will work quickly to fix it!',
					Response::HTTP_INTERNAL_SERVER_ERROR
				);
				break;

			default:
				$this->logger->debug($message);
				break;
		}
		return true;
	}

	/**
	 * @param \Exception $exception
	 * @return mixed
	 */
	public function handleException(Exception $exception) {
		ob_start();
		var_dump($exception);
		$this->logger->critical(sprintf('Exception Encountered:%s%s', PHP_EOL, ob_get_clean()));
		$this->failGracefully(
			'Oops! There was a problem serving the requested page.',
			'We\'ve been notified of the problem and will work quickly to fix it!',
			Response::HTTP_INTERNAL_SERVER_ERROR
		);
	}

	/**
	 * Registers a handler for errors and exceptions
	 *
	 * @return $this
	 */
	protected function registerErrorHandlers() {
		set_error_handler(array($this, 'handleError'));
		set_exception_handler(array($this, 'handleException'));
		return $this;
	}

	/**
	 * Fetches a string to represent the supplied PHp error level
	 *
	 * @param $code
	 * @return string
	 */
	protected function getErrorName($code) {
		return array_key_exists($code, $this->errorLevels)? $this->errorLevels[$code] : "UNKNOWN";
	}

	/**
	 * @param string $title
	 * @param string $message
	 * @param int    $httpStatusCode
	 * @param string $errorCode
	 */
	protected function failGracefully($title, $message='', $httpStatusCode=500, $errorCode='HTTP_INTERNAL_SERVER_ERROR') {
		$this->fire(new Event('application.graceful-failure', $this, $title));
		$html = sprintf('<!doctype HTML>
			<html>
			<head>
				<meta charset="utf-8">
				<meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
				<title>Oops! Something went wrong...</title>
				<style>
					* { font-family: "Helvetica Nueue", Helvetica, arial, sans-serif; color: #444; }
					html { background: #eee; }
					main { max-width: 600px; margin: auto; }
					div.clara-error-message {
						max-width: 600px;
						margin: 50px auto 25px;
						-webkit-box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
						-moz-box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
						box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
						padding: 24px;
						background: #fff;
						border: 1px solid #ccc;
						text-align: center;
					}
					h1 { font-size: 24px; font-weight: bold; }
					h2 { font-size: 16px; font-weight: normal; color: #888; }
					span { font-style: italic; font-size: 12px; text-align: center; color: #888; }
				</style>
			</head>
			<body>
				<header></header>
				<main>
					<div class="clara-error-message">
						<h1>%s</h1>
						<h2>%s</h2>
						<span>Error code: %s</span>
					</div>
				</main>
				<footer></footer>
			</body>
			</html>', $title, $message, $errorCode);
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