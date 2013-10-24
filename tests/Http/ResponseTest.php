<?php
/**
 * ResponseTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Http\Response;

class ResponseTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Clara\Http\Response::__construct
	 */
	public function testBasicObjectCreation() {
		$response = new Response('hello world!', 200, array('Foo' => 'Bar'));
		$this->assertAttributeSame('hello world!', 'body', $response);
		$this->assertAttributeSame(200, 'statusCode', $response);
		$this->assertAttributeSame(array('Foo' => 'Bar'), 'headers', $response);

		return $response;
	}

	/**
	 * @covers Clara\Http\Response::setStatusCode
	 * @expectedException InvalidArgumentException
	 * @dataProvider testBasicObjectCreation
	 */
	public function testNonNumericStatusCodeThrowsException(Response $response) {
		$response->setStatusCode('foo');
	}

	/**
	 * @covers Clara\Http\Response::setStatusCode
	 * @expectedException InvalidArgumentException
	 */
	public function testNonNumericStatusCodeInConstructorThrowsException() {
		$response = new Response('hello world!', 'invalid_response_code');
	}

	public function provideInvalidNumericResponseCodes() {
		return array(
			array(0),
			array(600),
			array(9001),
			array(-1),
			array(3.14159),
		);
	}

	/**
	 * Tests that numeric codes <100 or >=600 are rejected
	 *
	 * @covers Clara\Http\Response::setStatusCode
	 * @expectedException OutOfRangeException
	 * @dataProvider provideInvalidNumericResponseCodes
	 */
	public function testInvalidNumericStatusCodeThrowsException($code) {
		$response = new Response();
		$response->setStatusCode($code);
	}

	/**
	 * @test
	 * @covers Clara\Http\Response::getStatusText
	 */
	public function testGetStatusText() {
		$response = new Response();
		foreach(Response::$statusTexts as $code => $expectedText) {
			$response->setStatusCode($code);
			$text = $response->getStatusText();
			$this->assertSame($expectedText, $text);
		}
	}

	/**
	 * @covers Clara\Http\Response::__toString
	 */
	public function testToString() {
		$body = 'foobarbaztaz';
		$code = 302;
		$headers = array(
			'Foo' => 'Bar',
			'Baz' => 'Taz',
		);
		$expectedString = 'HTTP/1.1 ' . $code . ' ' . Response::$statusTexts[$code] . '\r\n';
		foreach($headers as $key => $val) {
			$expectedString .= sprintf('%s: %s\r\n', $key, $val);
		}
		$expectedString .= $body;

		$response = new Response($body, $code, $headers);
		$this->assertSame($expectedString, $response->__toString());
	}

	/**
	 * Due to limitations with PHPUnit, it's nearly impossible to test headers. Skipping this for now
	 *
	 * @covers Clara\Http\Response::sendHeaders
	 */
	public function testSendHeaders() {
		//todo $this->markTestIncomplete('Due to limitations with PHPUnit, it\'s nearly impossible to test headers. Will write a workaround in the future');
		/*
		$response = new Response();
		$response->setAllHeaders(array(
			'Foo' => 'Bar',
			'Baz' => 'Taz',
		));

		ob_start();
		header_remove();
		$response->sendHeaders();
		$headers_list = xdebug_get_headers();
		$this->assertNotEmpty($headers_list);
		$this->assertContains('Foo: Bar', $headers_list);
		$this->assertContains('Baz: Taz', $headers_list);
		ob_end_clean();
		*/
	}

	/**
	 * @covers Clara\Http\Response::sendBody
	 */
	public function testSendBody() {
		$response = new Response('hello, world!');
		ob_start();
		$response->sendBody();
		$actual = ob_get_clean();
		$this->assertSame('hello, world!', $actual);
	}

	/**
	 * @todo improve this test, as it's currently the same as the one above it.
	 *
	 * @covers Clara\Http\Response::send
	 */
	public function testSend() {
		$response = new Response('hello, world!', 302);
		ob_start();
		$response->send();
		$actual = ob_get_clean();
		$this->assertSame('hello, world!', $actual);
	}


}
