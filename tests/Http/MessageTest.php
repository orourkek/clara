<?php
/**
 * MessageTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Http\Message;

class MyMessage extends Message {}

class MessageTest extends PHPUnit_Framework_TestCase {


	/**
	 *
	 */
	public function testCreate() {
		$message = new MyMessage();
		$this->assertInstanceOf('Clara\Http\Message', $message);
		return $message;
	}

	/**
	 * @covers Clara\Http\Message::getAllHeaders
	 * @depends testCreate
	 */
	public function testHeadersAreInitiallyBlank(Message $message) {
		$this->assertAttributeEquals(array(), 'headers', $message);
		$this->assertEquals(array(), $message->getAllHeaders());
	}

	/**
	 * @covers Clara\Http\Message::setHeader
	 * @covers Clara\Http\Message::getHeader
	 * @depends testCreate
	 */
	public function testSetSingleHeader(Message $message) {
		$message->setHeader('foo', 'bar');
		$this->assertAttributeEquals(array('foo'=>'bar'), 'headers', $message);
		$this->assertEquals('bar', $message->getHeader('foo'));
		return $message;
	}

	/**
	 * @covers Clara\Http\Message::hasHeader
	 * @depends testSetSingleHeader
	 */
	public function testHasHeader(Message $message) {
		$this->assertTrue($message->hasHeader('foo'));
		$this->assertFalse($message->hasHeader('thisDoesntExist'));
	}

	/**
	 * @covers Clara\Http\Message::clearHeaders
	 * @depends testSetSingleHeader
	 */
	public function testClearHeaders(Message $message) {
		$message->clearHeaders();
		$this->assertAttributeEquals(array(), 'headers', $message);
		$this->assertEquals(array(), $message->getAllHeaders());
		return $message;
	}

	/**
	 * @covers Clara\Http\Message::setAllHeaders
	 * @depends testClearHeaders
	 */
	public function testSetAllHeaders(Message $message) {
		$headers = array(
			'foo' => 'bar',
			'baz' => 'taz',
		);
		$message->setAllHeaders($headers);

		$this->assertAttributeEquals($headers, 'headers', $message);
		$this->assertEquals($headers, $message->getAllHeaders());
	}

	/**
	 * @covers Clara\Http\Message::setAllHeaders
	 * @depends testClearHeaders
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetAllHeadersWithInvalidDataThrowsException(Message $message) {
		$message->setAllHeaders(null);
	}

	/**
	 * @covers Clara\Http\Message::getHeader
	 * @depends testCreate
	 * @expectedException \DomainException
	 */
	public function testGettingNonexistantHeaderThrowsException(Message $message) {
		$message->getHeader('thisDoesntExist');
	}

	/**
	 * @covers Clara\Http\Message::setProtocol
	 * @covers Clara\Http\Message::getProtocol
	 * @depends testCreate
	 */
	public function testSetProtocol(Message $message) {
		$protocol = 'HTTP/1.1';
		$message->setProtocol($protocol);
		$this->assertAttributeEquals($protocol, 'protocol', $message);
		$this->assertEquals($protocol, $message->getProtocol());
	}

	/**
	 * @covers Clara\Http\Message::setBody
	 * @covers Clara\Http\Message::getBody
	 * @depends testCreate
	 */
	public function testSetBody(Message $message) {
		$body = 'foobarbaztaz';
		$message->setBody($body);
		$this->assertAttributeEquals($body, 'body', $message);
		$this->assertEquals($body, $message->getBody());
	}

}