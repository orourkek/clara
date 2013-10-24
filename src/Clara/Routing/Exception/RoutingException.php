<?php
/**
 * RoutingException.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Routing\Exception;

use Clara\Exception\ClaraException;


/**
 * Class RoutingException
 *
 * @package Clara\Routing\Exception
 */
class RoutingException extends ClaraException {

	/**
	 * @var \Clara\Routing\Route
	 */
	protected $relevantRoute;

	/**
	 * @param \Clara\Routing\Route $relevantRoute
	 */
	public function setRelevantRoute($relevantRoute) {
		$this->relevantRoute = $relevantRoute;
	}

	/**
	 * @return \Clara\Routing\Route
	 */
	public function getRelevantRoute() {
		return $this->relevantRoute;
	}

}