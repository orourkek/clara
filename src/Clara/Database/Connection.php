<?php
/**
 * Connection.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Database;

use Clara\Database\Exception\DatabaseException;
use Clara\Events\Event;
use Clara\Events\Observable;
use Clara\Support\Contract\Stringable;
use PDO;
use PDOException;


/**
 * Class Connection
 *
 * @package Clara\Database
 */
abstract class Connection extends Observable {

	/**
	 * @see query()
	 */
	const FETCH_ROW = 1;

	/**
	 * @see query()
	 */
	const FETCH_ALL = 2;

	/**
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @param array  $options
	 * @throws \Clara\Database\Exception\DatabaseException
	 * @return $this
	 */
	public function connect($dsn, $username, $password, $options) {
		try {
			$this->fire(new Event('db.connection.connect', $this, $dsn));
			$this->pdo = new PDO($dsn, $username, $password, $options);
		} catch(PDOException $e) {
			$de = new DatabaseException('PDO Connection failed');
			$de->setPrevious($e);
			$this->fire(new Event('db.connection.error', $this, $de));
			throw $de;
		}
		return $this;
	}

	/**
	 * @param $query
	 * @return array|mixed
	 * @throws \Clara\Database\Exception\DatabaseException
	 */
	public function queryRow($query) {
		return $this->query($query, self::FETCH_ROW);
	}

	/**
	 * @param     $query
	 * @param int $fetch
	 * @return array|mixed
	 * @throws \Clara\Database\Exception\DatabaseException
	 */
	public function query($query, $fetch=self::FETCH_ALL) {
		if($query instanceof Stringable) {
			$query = (string) $query;
		} else if( ! is_string($query)) {
			throw new DatabaseException(sprintf('Query MUST be a string or implement Stringable, %s given', gettype($query)));
		}
		$this->fire(new Event('db.connection.query', $this, array('query'=>$query, 'mode'=>$fetch)));
		$result = $this->pdo->query($query);
		if(false === $result) {
			$this->errorOut($query);
		}
		switch($fetch) {
			case self::FETCH_ALL:
				return $result->fetchAll(PDO::FETCH_ASSOC);
				break;
			case self::FETCH_ROW:
				return $result->fetch(PDO::FETCH_ASSOC);
				break;
			default:
				$e = new DatabaseException(sprintf('Invalid fetch mode specified in %s', __METHOD__));
				$this->fire(new Event('db.connection.error', $this, $e));
				throw $e;
				break;
		}
	}

	/**
	 * @param $query
	 * @return int
	 * @throws \Clara\Database\Exception\DatabaseException
	 */
	public function exec($query) {
		if($query instanceof Stringable) {
			$query = (string) $query;
		} else if( ! is_string($query)) {
			throw new DatabaseException(sprintf('Query MUST be a string or implement Stringable, %s given', gettype($query)));
		}
		$this->fire(new Event('db.connection.exec', $this, $query));
		$count = $this->pdo->exec($query);
		return $count;
	}

	/**
	 * @param $query
	 * @return \PDOStatement
	 * @throws \Clara\Database\Exception\DatabaseException
	 */
	public function prepare($query) {
		if($query instanceof Stringable) {
			$query = (string) $query;
		} else if( ! is_string($query)) {
			throw new DatabaseException(sprintf('Query MUST be a string or implement Stringable, %s given', gettype($query)));
		}
		if($stmt = $this->pdo->prepare($query)) {
			return $stmt;
		}
		$this->errorOut($query);
	}

	/**
	 * @param string $query
	 * @throws \Clara\Database\Exception\DatabaseException
	 */
	protected function errorOut($query='') {
		$errorInfo = $this->pdo->errorInfo();
		$e = new DatabaseException(sprintf('PDO Encountered an error (%s): %s', $errorInfo[0], $errorInfo[2]));
		if( ! empty($query)) {
			$e->setRelevantQuery($query);
		}
		$this->fire(new Event('db.connection.error', $this, $e));
		throw $e;
	}
}