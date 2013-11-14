<?php
/**
 * Composer.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\View;

use Clara\Http\Response;
use Clara\Storage\Exception\FileNotFoundException;
use Clara\Storage\Filesystem;

/**
 * Composes Response object(s)
 *
 * NOTE:
 *
 * @package Clara\View
 */
abstract class Composer {

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var \Clara\Storage\Filesystem
	 */
	protected $filesystem;

	/**
	 * @var string
	 */
	protected $templatePath = '';

	/**
	 * @var array
	 */
	protected $templates = array();

	/**
	 * @var \Clara\Http\Response
	 */
	protected $response;

	/**
	 *
	 */
	public function __construct() {
		$this->filesystem = new Filesystem();
		$this->response = $this->createResponseObject();
	}

	/**
	 * @param integer $code
	 * @return $this
	 * @throws \OutOfRangeException
	 * @throws \InvalidArgumentException
	 */
	public function setStatusCode($code) {
		$this->response->setStatusCode($code);
		return $this;
	}

	/**
	 * @param $path
	 * @return $this
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function setTemplatePath($path) {
		if( ! $this->filesystem->isReadableDirectory($path)) {
			throw new FileNotFoundException('Template directory doesn\'t exist of isn\'t readable');
		}
		if('/' !== substr($path, -1)) {
			$path .= '/';
		}
		$this->templatePath = $path;
		return $this;
	}

	/**
	 * Adds data to the view
	 *
	 * @param string|integer|array $key
	 * @param null                 $value
	 * @return $this
	 */
	public function with($key, $value=null) {
		if (is_array($key)) {
			$this->data = array_merge($this->data, $key);
		} else {
			$this->data[$key] = $value;
		}
		return $this;
	}

	/**
	 * Adds a template file to the list of templates to be used
	 *
	 * @param $template
	 * @return $this
	 * @throws \Clara\Storage\Exception\FileNotFoundException
	 */
	public function withTemplate($template) {
		$fullPath = $this->templatePath . $template;
		if( ! $this->filesystem->isReadableFile($fullPath)) {
			throw new FileNotFoundException(sprintf('Template file unable to be found at %s', $fullPath));
		}
		$this->templates[] = $fullPath;
		return $this;
	}

	/**
	 * Composes a response object containing any templates assigned to the composer
	 *
	 * NOTE: Local variables in this method MUST be preceded with "__" to avoid naming collisions with extract()ed variables
	 *
	 * @return \Clara\Http\Response
	 */
	public function compose() {
		$body = $this->composeBody();
		$this->response->setHeader('Content-Length', strlen($body));
		$this->response->setHeader('Date', gmdate('D, d M Y H:i:s \G\M\T', time()));
		$this->response->setBody($body);
		$response = $this->response;
		//clear out the old response
		$this->response = $this->createResponseObject();
		return $response;
	}

	/**
	 * Returns any/all templates rendered as a string
	 *
	 * @return string
	 */
	protected function composeBody() {
		extract($this->data);
		ob_start();
		foreach($this->templates as $__template) {
			include $__template;
		}
		return ob_get_clean();
	}

	/**
	 * Creates a response object. Should be overridden in specific composer implementations to set applicable headers before returning the Response.
	 *
	 * @return \Clara\Http\Response
	 */
	protected function createResponseObject() {
		$response = new Response();
		return $response;
	}
}