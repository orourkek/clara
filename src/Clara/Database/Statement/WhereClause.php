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
use Clara\Support\Contract\Stringable;

/**
 * Used to build and represent a where clause in a MySQL statement.
 *
 * @package Clara\Database\Statement
 */
class WhereClause implements Stringable {

	/**
	 * List of operators that are able to be used
	 *
	 * @var array
	 */
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
	 * The conjunction to precede the clause, e.g. "OR".
	 * Not applicable to the first clause in a chain.
	 *
	 * @var string
	 */
	protected $preceder;

	/**
	 * The where clause target, e.g. "foo.bar" in "WHERE `foo`.`bar` LIKE '...'"
	 *
	 * @var \Clara\Database\Statement\Identifier
	 */
	protected $target;

	/**
	 * The clause operator. See WhereClause::validOperators[] for a list of valid operators
	 *
	 * @var string
	 */
	protected $operator;

	/**
	 * The clause predicate. Can be a string, Identifier, Statement, or anything that implements Stringable.
	 * Will be converted to string at time of assignment.
	 *
	 * @var string|\Clara\Database\Statement\Identifier
	 */
	protected $predicate;

	/**
	 * @param        $target
	 * @param string $operator
	 * @param string $predicate
	 * @param string $preceder
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
	 * Sets the clause operator. Must be in WhereClause::validOperators[]
	 *
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
	 * Returns the clause operator
	 *
	 * @return string
	 */
	public function getOperator() {
		return $this->operator;
	}

	/**
	 * Sets the clause predicate.
	 *
	 * The predicate can be any of the following:
	 *
	 *  1. subQuery/subStatement (of type Clara\Database\Statement\Statement)
	 *  2. literal value (string | int e.g. '123', 'foo', 123)
	 *  3. placeholder (e.g. '?', ':foo')
	 *  4. anything that implements \Clara\Support\Contract\Stringable
	 *
	 * @param mixed $predicate
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 * @return $this
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
		} else if($predicate instanceof Statement) {
			$this->predicate = $predicate->toStringAsSubQuery();
		} else if($predicate instanceof Stringable) {
			$this->predicate = (string) $predicate;
		} else {
			throw new StatementException(sprintf('Invalid WhereClause predicate. Expecting string|int|Statement, received %s', gettype($predicate)));
		}
		return $this;
	}

	/**
	 * Gets the clause predicate
	 *
	 * @return mixed
	 */
	public function getPredicate() {
		return $this->predicate;
	}

	/**
	 * Sets the clause preceder (e.g. "OR", "AND")
	 *
	 * @param string $preceder
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 * @return $this
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
	 * Gets the clause preceder
	 *
	 * @return string
	 */
	public function getPreceder() {
		return $this->preceder;
	}

	/**
	 * Sets the clause target (string or Identifier)
	 *
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
	 * Gets the clause target
	 *
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
	 * Attempts to construct a WhereClause object from a string
	 *
	 * @param string $str
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