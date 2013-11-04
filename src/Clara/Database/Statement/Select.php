<?php
/**
 * Select.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\Database\Statement;

use Clara\Database\Statement\Exception\StatementException;

/**
 * Class Select
 *
 * @package Clara\Database\Statement
 */
class Select extends Statement {

	/**
	 * @var \Clara\Database\Statement\Identifier[]
	 */
	protected $columns = array();

	/**
	 * @var \Clara\Database\Statement\Identifier[]
	 */
	protected $tables;

	/**
	 * @var \Clara\Database\Statement\WhereClause[]
	 */
	protected $whereClauses = array();

	/**
	 * @var \Clara\Database\Statement\OrderClause[]
	 */
	protected $orderClauses = array();

	/**
	 * @var string
	 */
	protected $limit = '';

	/**
	 * @param        $table
	 * @param string $alias
	 * @return $this
	 */
	public function from($table, $alias='') {
		$this->tables[] = new Identifier($table, $alias);
		return $this;
	}

	/**
	 * @param        $column
	 * @param string $alias
	 * @return $this
	 */
	public function column($column, $alias='') {
		$prefix = '';
		if(false !== strpos($column, '.')) {
			if(1 === preg_match('#^([0-9,a-z,A-Z$_]+?)\.([0-9,a-z,A-Z$_]+?)$#', $column, $matches)) {
				$column = $matches[2];
				$prefix = $matches[1];
			}
		}
		$this->columns[] = new Identifier($column, $alias, $prefix);
		return $this;
	}

	/**
	 * NOTE: THIS METHOD ACCEPTS A VARIABLE NUMBER OF ARGUMENTS VIA func_get_args()
	 *
	 * @return $this
	 */
	public function columns() {
		$columns = func_get_args();
		if( ! empty($columns)) {
			foreach($columns as $column) {
				if(is_array($column) && count($column) > 1) {
					$this->column($column[0], $column[1]);
				} else {
					$this->column($column);
				}
			}
		}
		return $this;
	}

	/**
	 * @param               $target
	 * @param null|string   $operator
	 * @param null|string   $predicate
	 * @param string        $clauseType
	 * @return $this
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 */
	public function where($target, $operator=null, $predicate=null, $clauseType='AND') {
		try {
			switch(true) {
				case (is_null($operator) && is_null($predicate)):
					$condition = WhereClause::fromString($target);
					break;
				case ( ! is_null($operator) && ! is_null($predicate)):
					$condition = new WhereClause($target, $operator, $predicate);
					break;
				default:
					throw new StatementException(sprintf('Invalid call to %s - see docblock for detailed description', __METHOD__));
					break;
			}
			if(count($this->whereClauses) > 0) {
				$condition->setPreceder($clauseType);
			}
			$this->whereClauses[] = $condition;
		} catch(\Exception $e) {
			$e = new StatementException(sprintf('Adding where condition failed'));
			$e->setPrevious($e);
			throw $e;
		}
		return $this;
	}

	/**
	 * @param             $target
	 * @param null|string $operator
	 * @param null|string $predicate
	 * @return $this
	 */
	public function orWhere($target, $operator=null, $predicate=null) {
		return $this->where($target, $operator, $predicate, 'OR');
	}

	/**
	 * @param             $target
	 * @param null|string $operator
	 * @param null|string $predicate
	 * @return $this
	 */
	public function andWhere($target, $operator=null, $predicate=null) {
		return $this->where($target, $operator, $predicate, 'AND');
	}

	/**
	 * @param integer      $arg1
	 * @param null|integer $arg2
	 * @return $this
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 */
	public function limit($arg1, $arg2=null) {
		if( ! is_int($arg1)
			|| ($arg1 <= 0)
			|| ( ! is_null($arg2) && ! is_int($arg2))
			|| ( ! is_null($arg2) && ($arg2 <= 0))
		) {
			throw new StatementException('The limit clause requires one or two non-negative integer arguments');
		}
		$this->limit = is_null($arg2) ? sprintf('%s', $arg1) : sprintf('%s,%s', $arg1, $arg2);
		return $this;
	}

	/**
	 * @param        $subject
	 * @param string $order
	 */
	public function orderBy($subject, $order='ASC') {
		$this->orderClauses[] = new OrderClause($subject, $order);
		return $this;
	}

	/**
	 * @return string
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 */
	public function compileStatement() {
		$str = 'SELECT ';

		// COLUMNS
		if(empty($this->columns)) {
			$str .= '* ';
		} else {
			$str .= implode(', ', $this->columns) . ' ';
		}

		//TABLE
		if(empty($this->tables)) {
			throw new StatementException('SELECT statements require at least one target table');
		}
		$str .= sprintf('FROM %s ', implode(', ', $this->tables));


		//CONDITION(S)
		if( ! empty($this->whereClauses)) {
			$str .= sprintf('WHERE %s ', implode(' ', $this->whereClauses));
		}

		//ORDER BY(S)
		if( ! empty($this->orderClauses)) {
			$str .= sprintf('ORDER BY %s ', implode(', ', $this->orderClauses));
		}

		//LIMIT
		if( ! empty($this->limit)) {
			$str .= sprintf('LIMIT %s ', $this->limit);
		}

		return trim($str);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->compileStatement();
	}
}
