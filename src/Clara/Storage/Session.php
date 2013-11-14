<?php
/**
 * Session.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Storage;

use Clara\Exception\ClaraInvalidArgumentException;

/**
 * Wrapper for $_SESSION interaction
 *
 * @package Clara\Storage
 */
class Session {

	/**
	 * Starts the session
	 *
	 * @codeCoverageIgnore
	 * @emits E_USER_ERROR
	 */
	public static function start() {
		if( ! session_id()) {
			if( ! session_start()) {
				trigger_error('Unable to start session', E_USER_ERROR);
			}
		}
	}

	/**
	 * Obliterates the session cookie AND session data.
	 *
	 * @codeCoverageIgnore
	 */
	public static function destroy() {
		if(ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				null,
				time() - 42000,
				$params["path"],
				$params["domain"],
				$params["secure"],
				$params["httponly"]
			);
		}
		session_destroy();
	}

	/**
	 * Gets content from the session using custom dot notation for array indices.
	 *
	 * NOTE: The $key parameter accepts dot notation
	 *
	 * DOT NOTATION:
	 *  Each dot (".") represents a step in the array
	 *  E.G.
	 *      Session::get('foo.bar.baz');
	 *      //will get the session content located at:
	 *      $_SESSION['foo']['bar']['baz']
	 *
	 * @param $key
	 * @throws \Clara\Exception\ClaraInvalidArgumentException
	 * @return mixed
	 */
	public static function get($key) {
		if( ! is_string($key) && ! is_integer($key)) {
			throw new ClaraInvalidArgumentException('Array keys should either be a string or integer');
		}
		if(false !== strpos($key, '.')) {
			$keys = explode('.', $key);
			$firstKey = array_shift($keys);
			if(array_key_exists($firstKey, $_SESSION)) {
				$subject =& $_SESSION[$firstKey];
				while(count($keys) > 0) {
					$k = array_shift($keys);
					if(( ! is_array($subject[$k]) && count($keys) >= 1) || ! array_key_exists($k, $subject)) {
						return null;
					}
					$subject =& $subject[$k];
				}
				return $subject;
			}
		}
		return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
	}

	/**
	 * Sets a session variable with the given value
	 *
	 * NOTE: The $key parameter accepts dot notation
	 *
	 * DOT NOTATION:
	 *  Each dot (".") represents a step in the array
	 *  E.G.
	 *      Session::set('foo.bar.baz', 'taz');
	 *      //is the same as saying:
	 *      $_SESSION['foo']['bar']['baz'] = 'taz';
	 *
	 * @param string|integer $key
	 * @param mixed          $value
	 * @return mixed
	 * @throws \Clara\Exception\ClaraInvalidArgumentException
	 */
	public static function set($key, $value) {
		if( ! is_string($key) && ! is_integer($key)) {
			throw new ClaraInvalidArgumentException('Array keys should either be a string or integer');
		}
		if(false !== strpos($key, '.')) {
			$keys = explode('.', $key);
			$current =& $_SESSION;
			foreach($keys as $k) {
				if( ! isset($current[$k])) {
					$current[$k] = array();
				}
				$current =& $current[$k];
			}
			return $current = $value;
		}
		return $_SESSION[$key] = $value;
	}

	/**
	 * Sets session content found at $key to NULL
	 *
	 * This method DOES NOT NECESSARILY DELETE THE CONTENT LIKE unset() DOES. Due to limitations with
	 * references (see @links below), it's not possible to use unset() to delete values from the
	 * session when dot notation is used here. Instead, setting the value to NULL will have to suffice.
	 *
	 * When deleting TOP LEVEL session values, unset() is used, because references never come into the picture.
	 *
	 * NOTE: The $key parameter accepts dot notation
	 *
	 * DOT NOTATION:
	 *  Each dot (".") represents a step in the array
	 *  E.G.
	 *      Session::delete('foo.bar.baz');
	 *      //is the same as saying:
	 *      $_SESSION['foo']['bar']['baz'] = NULL;
	 *
	 * @param string|integer$key
	 * @return void
	 * @throws \Clara\Exception\ClaraInvalidArgumentException
	 * @link http://php.net/manual/en/language.references.unset.php
	 * @link http://stackoverflow.com/a/1977734/1183986
	 */
	public static function delete($key) {
		if( ! is_string($key) && ! is_integer($key)) {
			throw new ClaraInvalidArgumentException('Array keys should either be a string or integer');
		}
		if(false !== strpos($key, '.')) {
			$keys = explode('.', $key);
			$firstKey = array_shift($keys);
			if(array_key_exists($firstKey, $_SESSION)) {
				$subject =& $_SESSION[$firstKey];
				while(count($keys) > 0) {
					$k = array_shift($keys);
					if(( ! is_array($subject[$k]) && count($keys) >= 1) || ! array_key_exists($k, $subject)) {
						return;
					}
					$subject =& $subject[$k];
				}
				$subject = null;
			}
		} else {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * Whether or not the Session has a value at the given key
	 *
	 * NOTE: The $key parameter accepts dot notation
	 *
	 * DOT NOTATION:
	 *  Each dot (".") represents a step in the array
	 *  E.G.
	 *      Session::has('foo.bar.baz');
	 *      //is the same as saying:
	 *      isset($_SESSION['foo']['bar']['baz']);
	 *
	 * @param string|integer $key
	 * @throws \Clara\Exception\ClaraInvalidArgumentException
	 * @return bool
	 */
	public static function has($key) {
		if( ! is_string($key) && ! is_integer($key)) {
			throw new ClaraInvalidArgumentException('Array keys should either be a string or integer');
		}
		if(false !== strpos($key, '.')) {
			$keys = explode('.', $key);
			$current =& $_SESSION;
			while(count($keys) > 0) {
				$k = array_shift($keys);
				if( ! isset($current[$k]) || ( ! is_array($current[$k]) && count($keys) >= 1)) {
					return false;
				}
				$current =& $current[$k];
			}
			return true;
		}
		return array_key_exists($key, $_SESSION);
	}

}