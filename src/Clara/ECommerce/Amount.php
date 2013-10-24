<?php
/**
 * Amount.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

namespace Clara\ECommerce;

use Clara\ECommerce\Exception\ValueException;


/**
 * This class represents a monetary amount. This class wraps around a float value with a
 * maximum of two decimal places, truncated via PHP's round() function in setValue()
 *
 * @package Clara\ECommerce
 */
class Amount {

	/**
	 * @var float
	 */
	protected $value;


	/**
	 * @param mixed $value
	 */
	public function __construct($value=0) {
		$this->setValue($value);
	}

	/**
	 * @return float
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param $value
	 * @throws \UnexpectedValueException
	 */
	public function setValue($value) {
		if( ! is_numeric($value)) {
			throw new ValueException('Invalid amount. Expecting numeric, received ' . gettype($value));
		}
		$this->value = round(floatval($value), 2);
	}

	/**
	 * @param Amount $amount
	 * @return $this
	 */
	public function merge(Amount $amount) {
		$this->value += $amount->getValue();
		return $this;
	}

	/**
	 * @param $amount
	 * @return $this
	 */
	public function add($amount) {
		if( ! $amount instanceof Amount) {
			$amount = new Amount($amount);
		}
		return $this->merge($amount);
	}

	/**
	 * @param $amount
	 * @return bool
	 */
	public function lessThan($amount) {
		if( ! $amount instanceof Amount) {
			$amount = new Amount($amount);
		}
		return ($this->value < $amount->getValue());
	}

	/**
	 * @param $amount
	 * @return bool
	 */
	public function lessThanOrEqual($amount) {
		if( ! $amount instanceof Amount) {
			$amount = new Amount($amount);
		}
		return ($this->value <= $amount->getValue());
	}

	/**
	 * @param $amount
	 * @return bool
	 */
	public function greaterThan($amount) {
		if( ! $amount instanceof Amount) {
			$amount = new Amount($amount);
		}
		return ($this->value > $amount->getValue());
	}

	/**
	 * @param $amount
	 * @return bool
	 */
	public function greaterThanOrEqual($amount) {
		if( ! $amount instanceof Amount) {
			$amount = new Amount($amount);
		}
		return ($this->value >= $amount->getValue());
	}

	/**
	 * @param $amount
	 * @return bool
	 */
	public function equalTo($amount) {
		if( ! $amount instanceof Amount) {
			$amount = new Amount($amount);
		}
		return ($this->value === $amount->getValue());
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return number_format($this->value, 2);
	}

}