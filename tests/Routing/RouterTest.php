<?php
/**
 * RouterTest.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Routing\Router;
use Clara\Routing\Route;
use Clara\Http\Request;
use \Clara\Events\Observer;
use \Clara\Events\Event;


class RouterObs extends Observer {
	public $witnessed = array();
	public function witness(Event $event) {
		$this->witnessed[] = $event;
	}
	public function hasWitnessed($str) {
		foreach($this->witnessed as $w) {
			if($str === $w->getName()) {
				return true;
			}
		}
		return false;
	}
}


/**
 * Class RouterTest
 */
class RouterTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Clara\Routing\Router::addRoute
	 */
	public function testAddRoute() {
		$router = new Router;
		$route = Route::get('/', function(){});
		$router->addRoute($route);
		$this->assertAttributeContains($route, 'routes', $router);
	}

	public function provideInvalidRoutes() {
		return array(
			array(false),
			array(null),
			array('str'),
			array(1234),
			array(3.14159),
			array(new StdClass),
		);
	}

	/**
	 * @covers Clara\Routing\Router::addRoute
	 * @expectedException Clara\Routing\Exception\RoutingException
	 * @dataProvider provideInvalidRoutes
	 */
	public function testAddRouteOnlyAcceptsRouteObjects($route) {
		$router = new Router;
		$router->addRoute($route);
	}

	/**
	 * @covers Clara\Routing\Router::importRoutesFromFile
	 */
	public function testLoadingRoutesFromFile() {
		$file = dirname(__DIR__) . '/mock/routes.php';
		$router = new Router();
		$router->importRoutesFromFile($file);
		$expectedRoutes = include $file;
		$this->assertAttributeEquals($expectedRoutes, 'routes', $router);
		return $router;
	}

	/**
	 * @covers Clara\Routing\Router::importRoutesFromFile
	 * @expectedException Clara\Routing\Exception\RoutingException
	 */
	public function testLoadingRoutesFailsWithInvalidFile() {
		$router = new Router();
		$router->importRoutesFromFile('this_does_not_exist');
	}

	/**
	 * @covers Clara\Routing\Router::importRoutesFromFile
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testLoadingRoutesFailsWithInvalidRouteInFile() {
		$file = dirname(__DIR__) . '/mock/routes_with_one_invalid.php';
		$router = new Router();
		$router->importRoutesFromFile($file);
	}

	/**
	 * @covers Clara\Routing\Router::route
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testRoutingWithNoRoutesLoadedReturnsFalseOnNoMatch() {
		$router = new Router();
		$this->assertFalse($router->route(new Request()));
	}

	/**
	 * @covers Clara\Routing\Router::route
	 * @depends testLoadingRoutesFromFile
	 */
	public function testRoutingWithRoutesLoadedReturnsFalseOnNoMatch(Router $router) {
		$this->assertFalse($router->route(new Request()));
	}

	/**
	 * @covers Clara\Routing\Router::route
	 * @depends testLoadingRoutesFromFile
	 */
	public function testRouteMatching(Router $router) {
		/*
		 * The routes file that was imported to the router SHOULD look like this:
		 *
		 *  Route::get('/foo', function(){})->setName('one'),
		 *	Route::post('/foo', function(){})->setName('two'),
		 *	Route::get('/foo/bar', function(){})->setName('three'),
		 *	Route::put('/foo/bar', function(){})->setName('four'),
		 *  Route::get('/foo', function(){})->setName('five'),
		 */

		$request = new Request();

		// should match
		$request->setMethod('GET')->setUri('/foo');
		$this->assertNotEquals(false, ($result = $router->route($request)));
		$this->assertTrue($result instanceof Route);
		$this->assertEquals('one', $result->getName());

		// should match
		$request->setMethod('POST')->setUri('/foo');
		$this->assertNotEquals(false, ($result = $router->route($request)));
		$this->assertTrue($result instanceof Route);
		$this->assertEquals('two', $result->getName());

		// should match

		/*
		 * TODO: Router is matching this to route 1, because the "$" modifier isn't used.
		 * This has to be fixed. Pick a way and fix it.
		 */

		$request->setMethod('GET')->setUri('/foo/bar');
		$this->assertNotEquals(false, ($result = $router->route($request)));
		$this->assertTrue($result instanceof Route);
		$this->assertEquals('three', $result->getName());

		// should match
		$request->setMethod('PUT')->setUri('/foo/bar');
		$this->assertNotEquals(false, ($result = $router->route($request)));
		$this->assertTrue($result instanceof Route);
		$this->assertEquals('four', $result->getName());

		// should NOT match
		$request->setMethod('GET')->setUri('/');
		$this->assertFalse($router->route($request));
	}

	/**
	 * @covers Clara\Routing\Router::route
	 * @depends testLoadingRoutesFromFile
	 */
	public function testMatchingStopsAtFirstMatch(Router $router) {
		// the duplicate routes known to the router are called 'one' and 'five' - see testRouteMatching() for more detailed info
		$request = new Request();
		$request->setMethod('GET')->setUri('/foo');

		$this->assertNotEquals(false, ($result = $router->route($request)));
		$this->assertTrue($result instanceof Route);
		$this->assertSame('one', $result->getName());
	}

	/**
	 * This method tests the specific hard-coded events that are fired from the router.
	 *
	 * See the Router class docblock for more info on the available events.
	 *
	 * @depends testLoadingRoutesFromFile
	 */
	public function testRouterSpecificObservableEventsAreFiring(Router $router) {
		$observer = new RouterObs();
		$router->attach($observer);
		$router->addRoute(Route::get('/oof/rab', function(){}));
		$this->assertTrue($observer->hasWitnessed('router.addRoute'));

		$request = new Request();

		// should match
		$request->setMethod('GET')->setUri('/oof/rab');
		$router->Route($request);
		$this->assertTrue($observer->hasWitnessed('router.route.success'));

		// should NOT match
		$request->setMethod('GET')->setUri('/qwertyuiop');
		$router->Route($request);
		$this->assertTrue($observer->hasWitnessed('router.route.failure'));
	}
}
