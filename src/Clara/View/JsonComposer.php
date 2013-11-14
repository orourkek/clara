<?php
/**
 * JsonComposer.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\View;

use Clara\Http\Response;

/**
 * Composes a Response object for use as JSON output
 *
 * @package Clara\View
 */
class JsonComposer extends Composer {

	/**
	 * @return string
	 */
	protected function composeBody() {
		if(empty($this->templates)) {
			return json_encode($this->data);
		} else {
			extract($this->data);
			ob_start();
			foreach($this->templates as $__template) {
				include $__template;
			}
			$content = ob_get_clean();
			return json_encode(array('response' => $content));
		}
	}

	/**
	 * Creates a response object with HTMl specific headers
	 *
	 * @return \Clara\Http\Response
	 */
	protected function createResponseObject() {
		$response = new Response();
		$response->setHeader('Content-Type', 'application/json');
		return $response;
	}

} 