<?php
/**
 * RequestTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Http\Request;
use Clara\Http\Uri;
use Clara\Support\Collection;


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

	/**
	 * @covers Clara\Http\Request::setGetVars
	 * @covers Clara\Http\Request::get
	 */
	public function testGetVars() {
		$vars = array(
			'foo' => 'bar',
			'baz' => 'taz',
		);
		$expected = new Collection($vars);
		$request = new Request();
		$request->setGetVars($vars);
		$this->assertAttributeEquals($expected, 'getVars', $request);

		$this->assertSame('bar', $request->get('foo'));
		$this->assertSame('taz', $request->get('baz'));
	}

	/**
	 * @covers Clara\Http\Request::setPostVars
	 * @covers Clara\Http\Request::post
	 */
	public function testPostVars() {
		$vars = array(
			'foo' => 'bar',
			'baz' => 'taz',
		);
		$expected = new Collection($vars);
		$request = new Request();
		$request->setPostVars($vars);
		$this->assertAttributeEquals($expected, 'postVars', $request);

		$this->assertSame('bar', $request->post('foo'));
		$this->assertSame('taz', $request->post('baz'));
	}

	/**
	 * @covers Clara\Http\Request::setCookies
	 * @covers Clara\Http\Request::cookie
	 */
	public function testCookies() {
		$vars = array(
			'foo' => 'bar',
			'baz' => 'taz',
		);
		$expected = new Collection($vars);
		$request = new Request();
		$request->setCookies($vars);
		$this->assertAttributeEquals($expected, 'cookies', $request);

		$this->assertSame('bar', $request->cookie('foo'));
		$this->assertSame('taz', $request->cookie('baz'));
	}

	/**
	 * @covers Clara\Http\Request::createFromEnvironment
	 */
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

		$_GET['foo'] = 'bar';
		$_POST['foo'] = 'bar';
		$_COOKIE['foo'] = 'bar';

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
		//check GET vars
		$this->assertSame('bar', $request->get('foo'));
		//check POST vars
		$this->assertSame('bar', $request->post('foo'));
		//check COOKIE vars
		$this->assertSame('bar', $request->cookie('foo'));
	}

}
