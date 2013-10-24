<?php
/**
 * UriTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Http\Uri;

class UriTest extends PHPUnit_Framework_TestCase {

	public function provideValidUris() {
		return array(
			array('http://google.com/foo/bar', 'http', '', '', 'google.com', '', '/foo/bar', '', ''),
			array('http://google.com/foo/bar?query1=baz#taz', 'http', '', '', 'google.com', '', '/foo/bar', 'query1=baz', 'taz'),
			array('https://127.0.0.1/foo', 'https', '', '', '127.0.0.1', '', '/foo', '', ''),
			array('http://user1@127.0.0.1:80/foo', 'http', 'user1', '', '127.0.0.1', '80', '/foo', '', ''),
			array('http://user1:password@127.0.0.1:80/foo', 'http', 'user1', 'password', '127.0.0.1', '80', '/foo', '', ''),
		);
	}

	/**
	 * @covers Clara\Http\Uri::parse
	 * @covers Clara\Http\Uri::getScheme
	 * @covers Clara\Http\Uri::getUser
	 * @covers Clara\Http\Uri::getPass
	 * @covers Clara\Http\Uri::getHost
	 * @covers Clara\Http\Uri::getPort
	 * @covers Clara\Http\Uri::getPath
	 * @covers Clara\Http\Uri::getQuery
	 * @covers Clara\Http\Uri::getFragment
	 * @dataProvider provideValidUris
	 */
	public function testCreate($uriString, $scheme, $user, $pass, $host, $port, $path, $query, $fragment) {
		$uri = new Uri($uriString);
		$this->assertInstanceOf('Clara\Http\Uri', $uri);
		$this->assertAttributeEquals($scheme, 'scheme', $uri);
		$this->assertAttributeEquals($user, 'user', $uri);
		$this->assertAttributeEquals($pass, 'pass', $uri);
		$this->assertAttributeEquals($host, 'host', $uri);
		$this->assertAttributeEquals($port, 'port', $uri);
		$this->assertAttributeEquals($path, 'path', $uri);
		$this->assertAttributeEquals($query, 'query', $uri);
		$this->assertAttributeEquals($fragment, 'fragment', $uri);

		$this->assertEquals($scheme, $uri->getScheme());
		$this->assertEquals($user, $uri->getUser());
		$this->assertEquals($pass, $uri->getPass());
		$this->assertEquals($host, $uri->getHost());
		$this->assertEquals($port, $uri->getPort());
		$this->assertEquals($path, $uri->getPath());
		$this->assertEquals($query, $uri->getQuery());
		$this->assertEquals($fragment, $uri->getFragment());
	}

	/**
	 * @covers Clara\Http\Uri::__toString
	 * @dataProvider provideValidUris
	 */
	public function testToString($uriString) {
		$uri = new Uri($uriString);
		$this->assertEquals($uriString, (string)$uri);
	}

	public function provideInvalidUriStrings() {
		return array(
			array(false),
			array(array()),
			array(null),
			array(3.14159),
			array(new \StdClass()),
		);
	}

	/**
	 * @dataProvider provideInvalidUriStrings
	 * @expectedException \DomainException
	 */
	public function testInvalidConstructorArgThrowsException($notAString) {
		$uri = new Uri($notAString);
	}

	/**
	 * @covers Clara\Http\Uri::startsWith
	 */
	public function testStartsWith() {
		$uri = new Uri('http://example.com/foo/bar');
		$this->assertTrue($uri->startsWith('http://example.com'));
		$this->assertTrue($uri->startsWith('h'));
		$this->assertTrue($uri->startsWith(''));
		$this->assertFalse($uri->startsWith('http://example.net'));
		$this->assertFalse($uri->startsWith('https://example.com'));
		$this->assertFalse($uri->startsWith('foo'));
		$this->assertFalse($uri->startsWith(null));
		$this->assertFalse($uri->startsWith(false));

		return $uri;
	}

	/**
	 * @covers Clara\Http\Uri::endsWith
	 * @depends testStartsWith
	 */
	public function testEndsWith($uri) {
		$this->assertTrue($uri->endsWith(''));
		$this->assertTrue($uri->endsWith('bar'));
		$this->assertTrue($uri->endsWith('/foo/bar'));
		$this->assertFalse($uri->endsWith('/'));
		$this->assertFalse($uri->endsWith('/foobar'));
		$this->assertFalse($uri->endsWith(null));
		$this->assertFalse($uri->endsWith(false));
		return $uri;
	}

	/**
	 * @covers Clara\Http\Uri::contains
	 * @depends testEndsWith
	 */
	public function testContains($uri) {
		$this->assertTrue($uri->contains(''));
		$this->assertTrue($uri->contains('/'));
		$this->assertTrue($uri->contains('/foo/bar'));
		$this->assertTrue($uri->contains('http://'));
		$this->assertTrue($uri->contains('example.com'));
		$this->assertFalse($uri->contains('/foobar/'));
		$this->assertFalse($uri->contains('example.net'));
		$this->assertFalse($uri->contains('https://'));
		$this->assertFalse($uri->contains(null));
		$this->assertFalse($uri->contains(false));
	}

}
