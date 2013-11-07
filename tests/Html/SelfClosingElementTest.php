<?php
/**
 * SelfClosingElementTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Html\Element;
use Clara\Html\SelfClosingElement;

class BazElement extends SelfClosingElement {
	protected $type = 'baz';
	protected $allowedAttributes = array('bar', 'baz');
}


class SelfClosingElementTest extends PHPUnit_Framework_TestCase {


	/**
	 * @covers \Clara\Html\SelfClosingElement::addContent
	 * @expectedException \Clara\Html\Exception\HtmlLogicException
	 */
	public function testAddingContentThrowsException() {
		$elem = new BazElement();
		$elem->addContent('foo');
	}

	/**
	 * @covers \Clara\Html\SelfClosingElement::open
	 */
	public function testOpenMethod() {
		$elem = new BazElement();
		$this->assertSame('<baz/>', $elem->open());
		$elem->addAttribute('id', 'foo');
		$this->assertSame('<baz id="foo"/>', $elem->open());
	}

	/**
	 * @covers \Clara\Html\SelfClosingElement::content
	 * @covers \Clara\Html\SelfClosingElement::close
	 */
	public function testSelfClosingElementsReturnNoContentAndClosingTag() {
		$elem = new BazElement();
		$this->assertSame('', $elem->content());
		$this->assertSame('', $elem->close());
	}

}
 