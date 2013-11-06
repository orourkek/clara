<?php
/**
 * MysqlConnection.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Database;


class MySQLConnection extends Connection {

	/**
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $dbName
	 * @param int    $port
	 */
	public function __construct($host, $user, $pass, $dbName, $port=3306) {
		$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $host, (int) $port, $dbName);
		$this->connect($dsn, $user, $pass, array());
	}

}