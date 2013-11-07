<?php
/**
 * ElementTest.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Html\Element;
use Clara\Html\Attribute;


class FooElement extends Element {
	protected $type = 'foo';
	protected $allowedAttributes = array('bar', 'baz');
}

class BarElement extends Element {
	protected $type = 'bar';
}

/**
 * Tests the fundamental behaviour of the abstract Element class, using the two mock classes above.
 */
class ElementTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 */
	public function testCreate() {
		$elem = new FooElement();
		$this->assertInstanceOf('\Clara\Html\Element', $elem);
	}

	public function provideAttributes() {
		return array(
			array('accesskey'),
			array('class'),
			array('contenteditable'),
			array('contextmenu'),
			array('data-foo'), //special case, see isValidWildcardAttribute
			array('data-barbaztaz'), //special case, see isValidWildcardAttribute
			array('dir'),
			array('draggable'),
			array('hidden'),
			array('id'),
			array('itemid'),
			array('itemprop'),
			array('itemref'),
			array('itemscope'),
			array('itemtype'),
			array('lang'),
			array('spellcheck'),
			array('style'),
			array('tabindex'),
			array('title'),
			array('bar'),
			array('baz'),
		);
	}

	/**
	 * @covers \Clara\Html\Element::addAttribute
	 * @covers \Clara\Html\Element::isValidAttribute
	 * @covers \Clara\Html\Element::isValidWildcardAttribute
	 * @dataProvider provideAttributes
	 */
	public function testAddAttributes($attr) {
		$elem = new FooElement();
		$elem->addAttribute($attr, 'foobar');
		$expected = array($attr => new Attribute($attr, 'foobar'));
		$this->assertAttributeEquals($expected, 'attributes', $elem);
	}

	/**
	 * This test was written in the endless battle to try to get 100% code coverage.
	 * In reality it's totally unnecessary because these methods are simple aliases
	 * for addAttribute(), but phpunit can be a bitch sometimes...
	 *
	 * @covers \Clara\Html\Element::id
	 * @covers \Clara\Html\Element::style
	 * @covers \Clara\Html\Element::clazz
	 */
	public function testAliasAttributeMethods() {
		$elem = new FooElement();
		$elem->id('foobar');
		$elem->clazz('baztaz');
		$elem->style('height:50px;');
		$expected = array(
			'id' => new Attribute('id', 'foobar'),
			'class' => new Attribute('class', 'baztaz'),
			'style' => new Attribute('style', 'height:50px;'),
		);
		$this->assertAttributeEquals($expected, 'attributes', $elem);
	}

	/**
	 * @covers \Clara\Html\Element::addAttribute
	 * @covers \Clara\Html\Element::isValidAttribute
	 * @covers \Clara\Html\Element::isValidWildcardAttribute
	 * @expectedException \Clara\Exception\ClaraDomainException
	 */
	public function testAddingInvalidAttributeThrowsException() {
		$elem = new FooElement();
		$elem->addAttribute('taz', 'foobar');
	}

	public function provideContent() {
		return array(
			array(new FooElement()),
			array('<br/>'),
			array('foobarbaztaz'),
		);
	}

	/**
	 * @covers \Clara\Html\Element::addContent
	 * @dataProvider provideContent
	 */
	public function testAddContent($content) {
		$elem = new FooElement();
		$elem->addContent($content);
		$this->assertAttributeContains($content, 'content', $elem);
	}

	/**
	 * @covers \Clara\Html\Element::__construct
	 * @dataProvider provideContent
	 */
	public function testAddContentThroughConstructor($content) {
		$elem = new FooElement($content);
		$this->assertAttributeContains($content, 'content', $elem);
	}

	/**
	 * @covers \Clara\Html\Element::__construct
	 * @dataProvider provideContent
	 */
	public function testAddContentArrayThroughConstructor() {
		$contentArray = array(
			new FooElement(),
			'<br/>',
			'foobarbaztaz',
		);
		$elem = new FooElement($contentArray);
		foreach($contentArray as $content) {
			$this->assertAttributeContains($content, 'content', $elem);
		}
	}

	public function provideInvalidContentTypes() {
		return array(
			array(false),
			array(true),
			array(null),
			array(3.14159),
			array(array()),
			array(new StdClass),
		);
	}

	/**
	 * @covers \Clara\Html\Element::addContent
	 * @dataProvider provideInvalidContentTypes
	 * @expectedException \Clara\Exception\ClaraDomainException
	 */
	public function testAddingInvalidContentTypeThrowsException($content) {
		$elem = new FooElement();
		$elem->addContent($content);
	}

	/**
	 * @covers \Clara\Html\Element::open
	 */
	public function testOpenMethod() {
		$elem = new FooElement();
		$this->assertSame('<foo>', $elem->open());
		$elem->addAttribute('id', 'bar');
		$this->assertSame('<foo id="bar">', $elem->open());
		$elem->addAttribute('class', 'baz');
		$this->assertSame('<foo id="bar" class="baz">', $elem->open());
	}

	/**
	 * @covers \Clara\Html\Element::content
	 */
	public function testContentMethod() {
		$elem = new FooElement();
		$this->assertSame('', $elem->content());
		$elem->addContent('hello, world!');
		$this->assertSame('hello, world!', $elem->content());
		$elem2 = new BarElement();
		$elem->addContent($elem2);
		$this->assertSame('hello, world!<bar></bar>', $elem->content());
	}

	/**
	 * @covers \Clara\Html\Element::close
	 */
	public function testCloseMethod() {
		$elem = new FooElement();
		$this->assertSame('</foo>', $elem->close());
	}

	public function provideElementsAndExpectedString() {
		$elem1 = new FooElement();
		$elem2 = new FooElement();
		$elem3 = new BarElement();
		$elem4 = new FooElement();
		$elem5 = new FooElement();

		return array(
			array(
				$elem1->addAttribute('id', 'bar')->addAttribute('data-bar', 'baz')->addContent('taz'),
				'<foo id="bar" data-bar="baz">taz</foo>',
			),
			array(
				$elem2->addAttribute('id', 'foo')->addContent($elem3),
				'<foo id="foo"><bar></bar></foo>',
			),
			array(
				$elem4->addContent('foo bar')->addContent($elem2),
				'<foo>foo bar<foo id="foo"><bar></bar></foo></foo>',
			),
			array(
				$elem5->addAttribute('data-foobarbaztaz', '')->addContent($elem4),
				'<foo data-foobarbaztaz=""><foo>foo bar<foo id="foo"><bar></bar></foo></foo></foo>',
			),
		);
	}


	/**
	 * tests a mix of simple elements, nested elements, and various attributes/content combinations
	 *
	 * @covers \Clara\Html\Element::__toString
	 * @dataProvider provideElementsAndExpectedString
	 */
	public function testToStringAsAWhole($elem, $expected) {
		$this->assertSame($expected, (string)$elem);
	}
}
 