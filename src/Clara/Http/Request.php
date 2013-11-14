<?php
/**
 * Request.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Http;

use Clara\Support\Collection;

/**
 * Represents an HTTP request, including all input ($_GET, $_POST, $_COOKIE, etc)
 *
 * @package Clara\Http
 */
class Request extends Message {

	/**
	 * HTTP method used for the request
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * Request URI (as an object)
	 *
	 * @var \Clara\Http\Uri
	 */
	protected $uri;

	/**
	 * Collection of $_GET values sent with the request
	 *
	 * @var \Clara\Support\Collection
	 */
	protected $getVars;

	/**
	 * Collection of $_POST values sent with the request
	 *
	 * @var \Clara\Support\Collection
	 */
	protected $postVars;

	/**
	 * Collection of $_COOKIE values sent with the request
	 *
	 * @var \Clara\Support\Collection
	 */
	protected $cookies;

	/**
	 *
	 */
	public function __construct() {
		$this->getVars = new Collection();
		$this->postVars = new Collection();
		$this->cookies = new Collection();
	}

	/**
	 * Creates a Request object from environment superglobals ($_SERVER, etc)
	 *
	 * @return \Clara\Http\Request
	 */
	public static function createFromEnvironment() {
		$s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
		$sp = strtolower($_SERVER['SERVER_PROTOCOL']);
		$protocol = substr($sp, 0, strpos($sp, '/')) . $s;
		$port = ($_SERVER['SERVER_PORT'] == '80') ? '' : (':'.$_SERVER['SERVER_PORT']);
		$fullUriString = $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
		
		$request = new Request();
		$request->setUri($fullUriString);
		$request->setMethod($_SERVER['REQUEST_METHOD']);
		$request->setProtocol($_SERVER['SERVER_PROTOCOL']);
		$request->setGetVars($_GET);
		$request->setPostVars($_POST);
		$request->setCookies($_COOKIE);

		if(function_exists("apache_request_headers") && $headers = apache_request_headers()) {
				/*
					let phpunit ignore this, because apache_request_headers() requires
					php to be loaded as an apache extension, which won't happen in most
					runs of phpunit
				*/
				// @codeCoverageIgnoreStart
				$request->setAllHeaders($headers);
				// @codeCoverageIgnoreEnd
		} else {
			foreach(array_keys($_SERVER) as $key) {
				if('HTTP_' === substr($key, 0, 5)) {
					$headername = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
					$request->setHeader($headername, $_SERVER[$key]);
				}
			}
		}
		return $request;
	}

	/**
	 * @param $method
	 * @return $this
	 */
	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param \Clara\Http\Uri|string $uri
	 * @return $this
	 * @throws \DomainException|\Exception
	 */
	public function setUri($uri) {
		if($uri instanceof Uri) {
			$this->uri = $uri;
		} else {
			try {
				$this->uri = new Uri($uri);
			} catch(\DomainException $e) {
				//TODO: what to do here?
				throw $e;
			}
		}
		return $this;
	}

	/**
	 * @return \Clara\Http\Uri
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * @param $vars
	 * @return $this
	 */
	public function setGetVars($vars) {
		if( ! is_array($vars)) {
			$vars = array($vars);
		}
		$this->getVars = new Collection($vars);
		return $this;
	}

	/**
	 * @param $vars
	 * @return $this
	 */
	public function setPostVars($vars) {
		if( ! is_array($vars)) {
			$vars = array($vars);
		}
		$this->postVars = new Collection($vars);
		return $this;
	}

	/**
	 * @param $vars
	 * @return $this
	 */
	public function setCookies($vars) {
		if( ! is_array($vars)) {
			$vars = array($vars);
		}
		$this->cookies = new Collection($vars);
		return $this;
	}

	/**
	 * Retrieves a value from $_GET
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->getVars[$key];
	}

	/**
	 * Retrieves a value from $_POST
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function post($key) {
		return $this->postVars[$key];
	}

	/**
	 * Retrieves a value from $_COOKIE
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function cookie($key) {
		return $this->cookies[$key];
	}

}