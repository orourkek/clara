<?php
/**
 * OrderClause.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Database\Statement;

use Clara\Database\Statement\Exception\StatementException;
use Clara\Support\Contract\Stringable;

/**
 * Used to build and represent an order clause in a MySQL statement.
 *
 * @package Clara\Database\Statement
 */
class OrderClause implements Stringable {

	/**
	 * The subject/target column for the ordering
	 *
	 * @var \Clara\Database\Statement\Identifier
	 */
	protected $subject;

	/**
	 * The order type: "ASC"|"DESC"
	 *
	 * @var string
	 */
	protected $order = 'ASC';

	/**
	 * @param        $subject
	 * @param string $order
	 */
	public function __construct($subject, $order='ASC') {
		$this->setSubject($subject);
		$this->setOrder($order);
	}

	/**
	 * @param $order
	 * @return $this
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 */
	public function setOrder($order) {
		$order = strtoupper($order);
		if( ! in_array($order, array('ASC', 'DESC'))) {
			throw new StatementException('Invalid order specified. Expecting ASC|DESC');
		}
		$this->order = $order;
		return $this;
	}

	/**
	 * @param \Clara\Database\Statement\Identifier $subject
	 * @return $this
	 */
	public function setSubject($subject) {
		$this->subject = Identifier::fromString($subject);
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return trim(sprintf('%s %s', $this->subject, $this->order));
	}

} 