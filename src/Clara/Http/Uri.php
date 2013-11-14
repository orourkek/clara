<?php
/**
 * Uri.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Http;

use DomainException;

/**
 * Represents a URI
 *
 * @package Clara\Http
 */
class Uri {

	/**
	 * @var string
	 */
	protected $scheme = '';
	/**
	 * @var string
	 */
	protected $user = '';
	/**
	 * @var string
	 */
	protected $pass = '';
	/**
	 * @var string
	 */
	protected $host = '';
	/**
	 * @var string
	 */
	protected $port = '';
	/**
	 * @var string
	 */
	protected $path = '';
	/**
	 * @var string
	 */
	protected $query = '';
	/**
	 * @var string
	 */
	protected $fragment = '';

	/**
	 * @param string $uriString
	 * @throws \DomainException
	 */
	public function __construct($uriString) {
		$parsed = $this->parse($uriString);
		if( ! $parsed || ! is_array($parsed)) {
			throw new DomainException('Invalid URI string given to ' . __METHOD__);
		}

		foreach($parsed as $key => $value) {
			$this->{$key} = $value;
		}
	}

	/**
	 * If the URI starts with $needle
	 *
	 * @param $needle
	 * @return bool
	 */
	public function startsWith($needle) {
		return ($needle === '') || (0 === strpos((string)$this, $needle));
	}

	/**
	 * If the URI ends with $needle
	 *
	 * @param $needle
	 * @return bool
	 */
	public function endsWith($needle) {
		return ($needle === '') || (substr((string)$this, -strlen($needle)) === $needle);
	}

	/**
	 * If the URI contains $needle
	 *
	 * @param $needle
	 * @return bool
	 */
	public function contains($needle) {
		return ($needle === '') || (false !== strpos((string)$this, $needle));
	}

	/**
	 * @return string
	 */
	public function getFragment() {
		return $this->fragment;
	}

	/**
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * @return string
	 */
	public function getPass() {
		return $this->pass;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * @return string
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * @return string
	 */
	public function getScheme() {
		return $this->scheme;
	}

	/**
	 * @return string
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @link http://tools.ietf.org/html/rfc3986#section-5.3
	 * @return string
	 */
	public function __toString() {
		$final = '';

		if($this->scheme) {
			$final .= $this->scheme . '://';
		}

		if($this->user) {
			$final .= $this->user;
			if($this->pass) {
				$final .= ':' . $this->pass;
			}
			$final .= '@';
		}

		if($this->host) {
			$final .= $this->host;
		}

		if($this->port) {
			$final .= ':' . $this->port;
		}

		if($this->path) {
			$final .= $this->path;
		}

		if($this->query) {
			$final .= '?' . $this->query;
		}

		if($this->fragment) {
			$final .= '#' . $this->fragment;
		}

		return $final;
	}

	/**
	 * Parses a URI string into components via parse_url
	 *
	 * @param $uriString
	 * @return array|bool
	 */
	protected function parse($uriString) {
		if(is_string($uriString)) {
			return parse_url($uriString);
		}
		return false;
	}

}