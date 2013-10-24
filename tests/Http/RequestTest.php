<?php
/**
 * RequestTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Http\Request,
	Clara\Http\Uri;


class RequestTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 */
	public function testCreate() {
		$request = new Request();
		$this->assertInstanceOf('Clara\Http\Request', $request);
		return $request;
	}

	/**
	 * @covers Clara\Http\Request::setMethod
	 * @covers Clara\Http\Request::getMethod
	 * @depends testCreate
	 */
	public function testSetMethod(Request $request) {
		$method = 'GET';
		$request->setMethod($method);
		$this->assertAttributeSame($method, 'method', $request);
		$this->assertSame($method, $request->getMethod());
	}

	public function provideValidUris() {
		return array(
			array('http://127.0.0.1/'),
			array(new Uri('http://foo/bar'))
		);
	}

	/**
	 * @covers Clara\Http\Request::setUri
	 * @covers Clara\Http\Request::getUri
	 * @dataProvider provideValidUris
	 */
	public function testSetUri($uri) {
		$request = new Request();
		$request->setUri($uri);

		if(is_string($uri)) {
			$this->assertSame($uri, (string)$request->getUri());
			//can't be assertAttributeSame because that also asserts that the reference is the same
			$this->assertAttributeEquals(new Uri($uri), 'uri', $request);
		} else {
			$this->assertSame($uri, $request->getUri());
			$this->assertAttributeSame($uri, 'uri', $request);
		}
	}

	public function testCreateFromEnvironment() {
		$_SERVER['HTTPS'] = 'on';
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['SERVER_PORT'] = '80';
		$_SERVER['SERVER_NAME'] = 'foobar.baz';
		$_SERVER['REQUEST_URI'] = '/taz?foo=1';
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,en;q=0.8';
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.76 Safari/537.36';
		$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
		$_SERVER['HTTP_CONNECTION'] = 'keep-alive';
		$expectedUri = 'https://foobar.baz/taz?foo=1';

		$request = Request::createFromEnvironment();
		//check type
		$this->assertInstanceOf('Clara\Http\Request', $request);
		//check URI
		$this->assertSame($expectedUri, (string)$request->getUri());
		//check protocol
		$this->assertAttributeSame($_SERVER['SERVER_PROTOCOL'], 'protocol', $request);
		//check headers
		$this->assertSame($_SERVER['HTTP_ACCEPT_LANGUAGE'], $request->getHeader('Accept-Language'));
		$this->assertSame($_SERVER['HTTP_USER_AGENT'], $request->getHeader('User-Agent'));
		$this->assertSame($_SERVER['HTTP_ACCEPT'], $request->getHeader('Accept'));
		$this->assertSame($_SERVER['HTTP_CONNECTION'], $request->getHeader('Connection'));
	}

}
