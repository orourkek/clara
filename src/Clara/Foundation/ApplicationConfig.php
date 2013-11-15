<?php
/**
 * ApplicationConfig.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Foundation;

use Clara\Support\Collection;

/**
 * Hold various config values for the Application. Can be expanded/extended to add further configuration to the Application class.
 *
 * @package Clara\Foundation
 */
class ApplicationConfig extends Collection {

	/**
	 * Array of application config values
	 *
	 * @var array
	 */
	protected $items = array(


		/**********************************************************************
		 * Debug Mode
		 **********************************************************************
		 *
		 * Toggles application debug mode
		 *
		 * Debug mode ON will attach an observer to the application and
		 * the router that will log all events to "debug.log" at the location
		 * defined in "logsDir" below.
		 */
		'debug' => false,


		/**********************************************************************
		 * Logs Directory
		 **********************************************************************
		 *
		 * The location (FULL PATH) where the application should place logs
		 */
		'logsDir' => '',


		/**********************************************************************
		 * Routes File
		 **********************************************************************
		 *
		 * The location (FULL PATH) of a routes file to be loaded into the
		 * application's router.
		 *
		 * See the routing README for information on route file formatting
		 */
		'routesFile' => '',


	);

	/**
	 * Creates a new ApplicationConfig object by merging values found in a config file with defaults
	 *
	 * @param string $location
	 * @return \Clara\Foundation\ApplicationConfig
	 */
	public static function fromFile($location) {
		$config = new self();
		$content = require $location;
		if(is_array($content) || $content instanceof Collection) {
			$config->merge($content);
		} else {
			trigger_error('Invalid application configuration file', E_USER_ERROR);
		}
		return $config;
	}

} 