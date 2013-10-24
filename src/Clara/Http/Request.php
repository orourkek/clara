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


/**
 * Class Request
 *
 * @package Clara\Http
 */
class Request extends Message {

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var \Clara\Http\Uri
	 */
	protected $uri;

	/**
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


}