<?php
/**
 * WhereClause.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Database\Statement;


use Clara\Database\Statement\Exception\StatementException;

class WhereClause {

	public static $validOperators = array(
		'=',
		'>=',
		'<=',
		'>',
		'<',
		'<>',
		'!=',
		'IN',
		'NOT IN',
		'LIKE',
		'NOT LIKE',
		'BETWEEN',
		'NOT BETWEEN',
		'IS',
		'IS NOT',
	);

	/**
	 * @var string
	 */
	protected $preceder;

	/**
	 * @var \Clara\Database\Statement\Identifier
	 */
	protected $target;

	/**
	 * @var string
	 */
	protected $operator;

	/**
	 * @var string|\Clara\Database\Statement\Identifier
	 */
	protected $predicate;

	/**
	 * @param        $target
	 * @param string $operator
	 * @param string $predicate
	 */
	public function __construct($target, $operator=null, $predicate=null, $preceder=null) {
		$this->setTarget($target);
		if( ! is_null($operator)) {
			$this->setOperator($operator);
		}
		if( ! is_null($predicate)) {
			$this->setPredicate($predicate);
		}
		if( ! is_null($preceder)) {
			$this->setPreceder($preceder);
		}
	}

	/**
	 * @param string $operator
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 * @return $this
	 */
	public function setOperator($operator) {
		$operator = strtoupper(trim($operator));
		if( ! in_array($operator, self::$validOperators)) {
			throw new StatementException(sprintf('Invalid WhereClause operator: "%s"', $operator));
		}
		$this->operator = $operator;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOperator() {
		return $this->operator;
	}

	/**
	 * The predicate can be any of the following:
	 *
	 *  1. subquery/substatement (of type Clara\Database\Statement\Statement)
	 *  2. literal value (string | int e.g. '123', 'foo', 123)
	 *  3. placeholder (e.g. '?', ':foo')
	 *
	 * @param mixed $predicate
	 */
	public function setPredicate($predicate) {
		if(is_numeric($predicate)) {
			$this->predicate = sprintf("'%u'", $predicate);
		} else if(is_string($predicate) &&  strlen($predicate) > 0) {
			if('?' === $predicate || 0 === strpos($predicate, ':')) {
				$this->predicate = $predicate;
			} else if(preg_match('#^(\'|")(.+)(\'|")$#', $predicate)) {
				//quoted string
				$this->predicate = $predicate;
			} else {
				$this->predicate = Identifier::fromString($predicate);
			}
		} else if(is_object($predicate) && $predicate instanceof \Clara\Database\Statement\Statement) {
			$this->predicate = $predicate->toStringAsSubQuery();
		} else {
			throw new StatementException(sprintf('Invalid WhereClause predicate. Expecting string|int|Statement, received %s', gettype($predicate)));
		}
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPredicate() {
		return $this->predicate;
	}

	/**
	 * @param string $preceder
	 */
	public function setPreceder($preceder) {
		$preceder = strtoupper($preceder);
		if( ! in_array($preceder, array('OR', 'AND'))) {
			throw new StatementException(sprintf('Invalid WhereClause preceder. Expecting AND|OR'));
		}
		$this->preceder = $preceder;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPreceder() {
		return $this->preceder;
	}

	/**
	 * @param mixed|\Clara\Database\Statement\Identifier $target
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 * @return $this
	 */
	public function setTarget($target) {
		try {
			$this->target = Identifier::fromString($target);
		} catch(StatementException $prev) {
			$e = new StatementException('WhereClause target must be a valid identifier');
			$e->setPrevious($prev);
			throw $e;
		}
		return $this;
	}

	/**
	 * @return \Clara\Database\Statement\Identifier
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return trim(sprintf('%s %s %s %s', $this->preceder, $this->target, $this->operator, $this->predicate));
	}

	/**
	 * @param $str
	 * @return \Clara\Database\Statement\WhereClause
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 */
	public static function fromString($str) {
		if(is_string($str)) {
			try {
				switch(count($pieces = explode(' ', $str))) {
					case 1:
						return new WhereClause($pieces[0]);
						break;
					case 3:
						return new WhereClause($pieces[0], $pieces[1], $pieces[2]);
						break;
				}
			} catch(\Exception $e) {}
		}
		throw new StatementException(sprintf('WhereClause::fromString failed with input "%s"', $str));
	}
} 