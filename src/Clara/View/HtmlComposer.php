<?php
/**
 * HtmlComposer.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\View;

use Clara\Http\Response;

/**
 * Composes a Response object for use as HTML output
 *
 * @package Clara\View
 */
class HtmlComposer extends Composer {

	/**
	 * Creates a response object with HTMl specific headers
	 *
	 * @return \Clara\Http\Response
	 */
	protected function createResponseObject() {
		$response = new Response();
		$response->setHeader('Content-Type', 'text/html');
		return $response;
	}

} 