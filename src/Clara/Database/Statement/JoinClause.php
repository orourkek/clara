<?php
/**
 * JoinClause.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     clara
 */

namespace Clara\Database\Statement;

use Clara\Database\Statement\Exception\StatementException;
use Clara\Support\Contract\Stringable;

/**
 * Used to build and represent a JOIN clause in a MySQL statement.
 * Note: This class is incomplete, and does NOT contain all available MySQL join operations/syntaxes.
 *
 * @package Clara\Database\Statement
 * @todo Continue to add support for more JOIN syntaxes
 */
class JoinClause implements Stringable {

	/**
	 * List of valid JOIN types
	 *
	 * @var array
	 */
	public static $validTypes = array(
		'INNER',
		'LEFT OUTER',
		'RIGHT OUTER',
		'FULL OUTER',
	);

	/**
	 * @var \Clara\Database\Statement\Identifier
	 */
	protected $target;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var \Clara\Database\Statement\ConditionalExpression[]
	 */
	protected $on = array();

	/**
	 * @param        $target
	 * @param string $type
	 */
	public function __construct($target, $type='INNER') {
		$this->setTarget($target);
		$this->setType($type);
	}

	/**
	 * @param string|\Clara\Database\Statement\Identifier $target
	 * @throws \Clara\Database\Statement\Exception\StatementException
	 * @return $this
	 */
	public function setTarget($target) {
		try {
			$this->target = Identifier::fromString($target);
		} catch(StatementException $prev) {
			$e = new StatementException('JoinClause target must be a valid identifier');
			$e->setPrevious($prev);
			throw $e;
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
	public function on($target, $operator=null, $predicate=null, $clauseType='AND') {
		try {
			switch(true) {
				case (is_null($operator) && is_null($predicate)):
					$condition = ConditionalExpression::fromString($target);
					break;
				case ( ! is_null($operator) && ! is_null($predicate)):
					$condition = new ConditionalExpression($target, $operator, $predicate);
					break;
				default:
					throw new StatementException(sprintf('Invalid call to %s - see docblock for detailed description', __METHOD__));
					break;
			}
			if(count($this->on) > 0) {
				$condition->setPreceder($clauseType);
			}
			$this->on[] = $condition;
		} catch(\Exception $e) {
			$e = new StatementException(sprintf('Adding ON clause failed'));
			$e->setPrevious($e);
			throw $e;
		}
		return $this;
	}

	/**
	 * @param string $type
	 * @throws Exception\StatementException
	 * @return $this
	 */
	public function setType($type) {
		if( ! in_array($type, static::$validTypes)) {
			throw new StatementException(sprintf('Invalid join type specified. Expecting %s', implode('|', static::$validTypes)));
		}
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		$on = empty($this->on) ? '' : sprintf('ON %s', implode(' ', $this->on));
		return sprintf('%s JOIN %s %s', $this->type, $this->target, $on);
	}

} 