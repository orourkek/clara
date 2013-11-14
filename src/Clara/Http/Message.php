<?php
/**
 * Message.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Http;

use DomainException;
use InvalidArgumentException;

/**
 * Represents a generic HTTP message
 *
 * @package Clara\Http
 */
abstract class Message {

	const METHOD_HEAD = 'HEAD';
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';
	const METHOD_OPTIONS = 'OPTIONS';

	/**
	 * The HTTP protocol used for the message
	 *
	 * @var string
	 */
	protected $protocol = 'HTTP/1.1';

	/**
	 * Headers attached to the message
	 *
	 * @var array
	 */
	protected $headers = array();

	/**
	 * Body of the message
	 *
	 * @var string
	 */
	protected $body = '';

	/**
	 * @param $field
	 * @param $value
	 * @return $this
	 */
	public function setHeader($field, $value) {
		$this->headers[$field] = $value;
		return $this;
	}

	/**
	 * @param $field
	 * @return bool
	 */
	public function hasHeader($field) {
		return isset($this->headers[$field]);
	}

	/**
	 * @param $field
	 * @return mixed
	 * @throws \DomainException
	 */
	public function getHeader($field) {
		if(isset($this->headers[$field])) {
			return $this->headers[$field];
		}
		throw new DomainException('Specified header field does not exist: ' . $field);
	}

	/**
	 * @return array
	 */
	public function getAllHeaders() {
		return $this->headers;
	}

	/**
	 * Sets all headers, replacing any existing
	 *
	 * @param $headers
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setAllHeaders($headers) {
		if( ! is_array($headers) && ! $headers instanceof \Traversable) {
			throw new InvalidArgumentException('Setting all headers requires a traversable list');
		}
		$this->clearHeaders();
		foreach($headers as $field => $value) {
			$this->setHeader($field, $value);
		}
		return $this;
	}

	/**
	 * Clears all headers from the message
	 *
	 * @return $this
	 */
	public function clearHeaders() {
		$this->headers = array();
		return $this;
	}

	/**
	 * @param $protocol
	 * @return $this
	 */
	public function setProtocol($protocol) {
		$this->protocol = $protocol;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @param $body
	 * @return $this
	 */
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}


}