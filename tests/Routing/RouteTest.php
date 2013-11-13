<?php
/**
 * RouteTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Routing\Route;
use Clara\Http\Request;


class hasCallable {
	public static function staticFoo() {}
	public function foo() {}
}
class tester {
	public function foo() { return func_get_args(); }
	public static function staticFoo() { return func_get_args(); }
}

class RouteTest extends PHPUnit_Framework_TestCase {

	public function assertRouteHasMethod(Route $route, $method) {
		$this->assertAttributeContains($method, 'methods', $route);
		$this->assertContains($method, $route->getMethods());
	}

	/**
	 * @covers \Clara\Routing\Route::setName
	 * @covers \Clara\Routing\Route::getName
	 */
	public function testNaming() {
		$route = Route::get('/', function(){});
		$route->setName('foobar');
		$this->assertAttributeSame('foobar', 'name', $route);
		$this->assertSame('foobar', $route->getName());
	}

	/**
	 * Tests the basic manual construction of a route, via `new`
	 *
	 * @covers \Clara\Routing\Route::__construct
	 * @covers \Clara\Routing\Route::setMethods
	 * @covers \Clara\Routing\Route::getMethods
	 * @covers \Clara\Routing\Route::setPattern
	 * @covers \Clara\Routing\Route::getPattern
	 * @covers \Clara\Routing\Route::setHandler
	 * @covers \Clara\Routing\Route::getHandler
	 */
	public function testManualConstruction() {
		$methods = array('GET', 'POST');
		$pattern = '/foo/bar';
		$handler = function(){};

		$route = new Route($methods, $pattern, $handler);

		//check methods
		$this->assertRouteHasMethod($route, 'GET');
		$this->assertRouteHasMethod($route, 'POST');
		$this->assertSame($methods, $route->getMethods());

		//check pattern
		$this->assertAttributeSame($pattern, 'pattern', $route);
		$this->assertSame($pattern, $route->getPattern());

		//check handler
		$this->assertAttributeNotEmpty('handler', $route);
		$this->assertAttributeSame($handler, 'handler', $route);
		$this->assertSame($handler, $route->getHandler());
	}

	/**
	 * @covers \Clara\Routing\Route::get
	 * @covers \Clara\Routing\Route::post
	 * @covers \Clara\Routing\Route::delete
	 * @covers \Clara\Routing\Route::put
	 * @covers \Clara\Routing\Route::canHandleMethod
	 */
	public function testStaticConstruction() {
		$pattern = '/foo/bar';
		$handler = function(){};
		$methodsToTest = array(
			'get',
			'post',
			'delete',
			'put',
		);
		foreach($methodsToTest as $func) {
			$route = Route::$func($pattern, $handler);
			$this->assertRouteHasMethod($route, strtoupper($func));
			$this->assertAttributeSame($pattern, 'pattern', $route);
			$this->assertTrue($route->canHandleMethod($func));
			$this->assertAttributeNotEmpty('handler', $route);
		}
	}

	public function provideHandlers() {
		return array(
			array(function(){}, false),
			array(array(new HasCallable(), 'foo'), false),
			array('hasCallable::staticFoo', false),
			array('hasCallable@foo', false),
			array('', true),
			array(false, true),
			array(null, true),
			array(array(), true),
			array(new hasCallable(), true),
		);
	}

	/**
	 * @covers \Clara\Routing\Route::setHandler
	 * @dataProvider provideHandlers
	 */
	public function testSetHandler($handler, $expectingException) {
		if($expectingException) {
			$this->setExpectedException('Clara\Routing\Exception\RoutingException');
			$route = Route::get('/', $handler);
		} else {
			$route = Route::get('/', $handler);
			$this->assertAttributeNotEmpty('handler', $route);
		}

	}

	/**
	 * @covers \Clara\Routing\Route::compileRegex
	 */
	public function testBasicRegexConstruction() {
		$pattern = '/foo/bar';
		$handler = function(){};

		$route = Route::get($pattern, $handler);
		$this->assertAttributeSame('#^/foo/bar$#i', 'regex', $route);
	}

	/**
	 * @covers \Clara\Routing\Route::compileRegex
	 * @covers \Clara\Routing\Route::getRegex
	 */
	public function testRegexConstructionWithParameters() {
		$pattern = '/foo/bar/{variable1}/{variable2}';
		$handler = function(){};
		$request = new Request();
		$request->setMethod('GET');
		$request->setUri('/foo/bar/baz/taz');

		$route = Route::get($pattern, $handler);
		$this->assertAttributeSame('#^/foo/bar/(?P<variable1>.+)/(?P<variable2>.+)$#i', 'regex', $route);
		$this->assertSame('#^/foo/bar/(?P<variable1>.+)/(?P<variable2>.+)$#i', $route->getRegex());
		$this->assertTrue($route->matches($request));
		//matches() should also pull the variable values from the uri:
		$this->assertSame(array('variable1' => 'baz', 'variable2' => 'taz'), $route->getParameters());
		$this->assertSame(array('baz', 'taz'), $route->getParameterValues());
		$this->assertSame(array('variable1', 'variable2'), $route->getParameterKeys());

		$request->setUri('this_will_not_match');
		$this->assertFalse($route->matches($request));
	}

	/**
	 * @covers \Clara\Routing\Route::compileRegex
	 * @expectedException Clara\Routing\Exception\RoutingException
	 */
	public function testDuplicateParametersNotAllowed() {
		Route::get('/{foo}/{bar}/{foo}', function(){});
	}


	/**
	 * @covers \Clara\Routing\Route::setRegex
	 * @expectedException Clara\Routing\Exception\RoutingException
	 */
	public function testRegexIsImmutable() {
		$route = Route::get('/foo', function(){});
		$route->setRegex('exception time!');
	}

	public function provideRoutesForMatchingTests() {
		return array(
			array('GET', '/foo/bar', true),
			array('GET', '/foo/123', true),
			array('GET', '/foo/bar/123', true),
			array('GET', '/foobar', false),
			array('GET', '/foo', false),
			array('GET', '/bar', false),
			array('GET', '/', false),
			array('GET', '', false),
			array('POST', '/foo/bar', false),
			array('PUT', '/foo/bar', false),
			array('DELETE', '/foo/bar', false),
		);
	}

	/**
	 * @covers \Clara\Routing\Route::matches
	 * @dataProvider provideRoutesForMatchingTests
	 */
	public function testRouteMatching($method, $uri, $expectedResult) {
		$request = new Request();
		$request->setUri($uri)->setMethod($method);
		$route = Route::get('/foo/{var}', function(){});
		$this->assertSame($expectedResult, $route->matches($request));
	}

	/**
	 * @test
	 * @covers \Clara\Routing\Route::matches
	 * @covers \Clara\Routing\Route::compileRegex
	 */
	public function partialPatternMatchesShouldNotBeMatched() {
		$request = new Request();
		$request->setUri('/foo/bar')->setMethod('GET');
		$route = Route::get('/foo/bar/{var}', function(){});
		$this->assertFalse($route->matches($request));
	}

	/**
	 * @covers \Clara\Routing\Route::matches
	 */
	public function testMatchingRouteWithMultipleMethods() {
		$route = new Route(array('GET', 'POST'), '/foo/{var}', function(){});
		$request = new Request();
		$request->setUri('/foo/bar')->setMethod('GET');
		$this->assertTrue($route->matches($request));
		$request->setMethod('POST');
		$this->assertTrue($route->matches($request));
	}

	/**
	 * @covers \Clara\Routing\Route::compileRegex
	 * @covers \Clara\Routing\Route::getParameters
	 * @covers \Clara\Routing\Route::getParameterValues
	 * @covers \Clara\Routing\Route::getParameterKeys
	 */
	public function testParametersAndMatchingWithParameters() {
		$request = new Request();
		$request->setMethod('GET');
		$request->setUri('/foo/bar/baz/taz');

		$pattern = '/foo/bar/{variable}/{variable2}';
		$handler = function(){};
		$route = Route::get($pattern, $handler);

		$this->assertTrue($route->matches($request));
		//matches() should also pull the variable values from the uri:
		$this->assertSame(array('variable' => 'baz', 'variable2' => 'taz'), $route->getParameters());
		$this->assertSame(array('baz', 'taz'), $route->getParameterValues());
		$this->assertSame(array('variable', 'variable2'), $route->getParameterKeys());

		$request->setUri('this_will_not_match');
		$this->assertFalse($route->matches($request));
	}

	/**
	 * @covers \Clara\Routing\Route::patternIsValid
	 */
	public function testStaticPatternIsValidMethod() {
		$this->assertTrue(Route::patternIsValid('/'));
		$this->assertTrue(Route::patternIsValid('/foo'));
		$this->assertTrue(Route::patternIsValid('/foo/{var}'));
		$this->assertTrue(Route::patternIsValid('foo/{var}'));

		$this->assertFalse(Route::patternIsValid('/{}_()*^&%&^$%#$@@$%&*('));
		$this->assertFalse(Route::patternIsValid('ɏɕʥω'));
		$this->assertFalse(Route::patternIsValid(null));
		$this->assertFalse(Route::patternIsValid(false));
		$this->assertFalse(Route::patternIsValid(3.14159));
		$this->assertFalse(Route::patternIsValid(array()));
	}

	/**
	 * @covers \Clara\Routing\Route::setPattern
	 * @expectedException Clara\Routing\Exception\RoutingException
	 */
	public function testInvalidPatternThrowsExceptionFromConstructor() {
		$invalidPattern = '/{}_()*^&%&^$%#$@@$%&*(';
		Route::get($invalidPattern, function(){});
	}

	/**
	 * @covers \Clara\Routing\Route::setPattern
	 * @expectedException Clara\Routing\Exception\RoutingException
	 */
	public function testInvalidPatternThrowsExceptionFromSetter() {
		$invalidPattern = '/{}_()*^&%&^$%#$@@$%&*(';
		$route = Route::get('/valid/pattern', function(){});
		$route->setPattern($invalidPattern);
	}

	public function provideVariousRoutesWithArgs() {
		return array(
			array(new Route('GET', '/foo/{var1}/{var2}', function() { return func_get_args(); })),
			array(new Route('GET', '/foo/{var1}/{var2}', 'tester@foo')),
			array(new Route('GET', '/foo/{var1}/{var2}', 'tester::staticFoo')),
			array(new Route('GET', '/foo/{var1}/{var2}', array(new tester(), 'foo'))),
		);
	}

	/**
	 * @covers \Clara\Routing\Route::run
	 * @dataProvider provideVariousRoutesWithArgs
	 */
	public function testRunSendsArgs(Route $route) {
		$request = new Request();
		$request->setUri('/foo/bar/baz')->setMethod('GET');

		if($route->matches($request)) {
			$result = $route->run();
			$this->assertSame(array('bar', 'baz'), $result);
		} else {
			$this->fail('Route failed to match request, something is seriously wrong here...');
		}
	}

}
