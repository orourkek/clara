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
use Clara\View\HtmlComposer;
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
		$this->router = $this->makeRouter();
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
			if($response instanceof Response) {
				$response->send();
			}
		} else {
			$this->fire(new Event('application.run.not-found', $this));
			$this->logger->notice(sprintf('404 Not Found: %s', $request->getUri()));
			$this->failGracefully(
				'404 Not Found',
				sprintf('The requested URL %s was not found on this server', $request->getUri()->getRequestUri()),
				Response::HTTP_NOT_FOUND,
				'HTTP_NOT_FOUND',
				$request
			);
		}
		$this->fire(new Event('application.run.complete', $this, $request));
	}

	/**
	 * @param       $level
	 * @param       $message
	 * @param       $file
	 * @param       $line
	 * @param array $context
	 * @return bool
	 */
	public function handleError($level, $message, $file, $line, $context=array()) {
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
					Response::HTTP_INTERNAL_SERVER_ERROR,
					'HTTP_INTERNAL_SERVER_ERROR',
					compact('level', 'message', 'file', 'line')
				);
				break;

			case E_CORE_ERROR:
				$this->logger->alert($message);
				$this->failGracefully(
					'Oops! There was a problem serving the requested page.',
					'We\'ve been notified of the problem and will work quickly to fix it!',
					Response::HTTP_INTERNAL_SERVER_ERROR,
					'HTTP_INTERNAL_SERVER_ERROR',
					compact('level', 'message', 'file', 'line')
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
			Response::HTTP_INTERNAL_SERVER_ERROR,
			'HTTP_INTERNAL_SERVER_ERROR',
			$exception
		);
	}

	/**
	 * Catches fatal errors and makes sure the event is logged, and a proper (user-friendly) response is shown
	 */
	public function handleShutdown() {
		if( ! headers_sent() && $e = error_get_last()) {
			$this->handleError($e['type'], $e['message'], $e['file'], $e['line']);
		}
	}

	/**
	 * Registers a handler for errors, exceptions, and shutdowns (fatal errors)
	 *
	 * @return $this
	 */
	protected function registerErrorHandlers() {
		set_error_handler(array($this, 'handleError'));
		set_exception_handler(array($this, 'handleException'));
		register_shutdown_function(array($this, 'handleShutdown'));
		return $this;
	}

	/**
	 * Creates a Router object
	 *
	 * @return \Clara\Routing\Router
	 */
	protected function makeRouter() {
		return new Router();
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
	 * @param null   $reason
	 */
	protected function failGracefully($title, $message='', $httpStatusCode=500, $errorCode='HTTP_INTERNAL_SERVER_ERROR', $reason=null) {
		//todo: make this event more verbose about what happened
		$this->fire(new Event('application.graceful-failure', $this, $title));
		$composer = new HtmlComposer();
		$composer->withTemplate(dirname(__DIR__) . '/View/templates/application-failure.php');
		$composer->with(compact('title', 'message', 'errorCode'));
		if($this->debugMode) {
			$composer->with('debugInfo', $reason);
		}
		try {
			$composer->setStatusCode($httpStatusCode);
		} catch(Exception $e) {
			$composer->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		$response = $composer->compose();
		$response->send();
		exit(2);
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
			require CLARA_LIB_DIR . '/kint/Kint.class.php';
			$this->fire(new Event('application.debug-on', $this));
		}

		$this->router->importRoutesFromFile($this->config['routesFile']);
		$this->fire(new Event('application.routes-loaded', $this, $this->config['routesFile']));

		return $this;
	}

}