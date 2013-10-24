Clara\Routing
=============

This is the routing code of Clara


Overview
--------

This namespace contains the following classes:

* `Clara\Routing\Route` - Contains the route definition (pattern, methods, etc) and the handler class/method for the route.
* `Clara\Routing\Router` - The router matches an `Clara\Http\Request` to the correct `Clara\Routing\Route` (if one exists)


The Route Object
----------------

### Components

The most critical parts of a Route object are the pattern, handler, and method(s). Every route MUST have these three things in order to be valid.

#### Pattern

This is a specially formatted string that will be internally converted into a regular expression. The pattern can ONLY contain:

1. Alphanumeric characters (\w in regex talk)
2. Variable holders of the form `{varName}`

Variables are converted to named subpatterns that match using `(.+)`. When `Clara\Routing\Route::matches` is called, these variables are placed into the `parameters` property of the route, and **will be passed as arguments to the handler when the route is dispatched.**

#### Handler

The handler is a [callable](http://php.net/manual/en/language.types.callable.php) that will handle the request when the route is dispatched.

Example handlers:

	<?php
	$handler = 'className::staticMethod';
	$handler = array($object, 'methodName');
	$handler = function() { ... };

#### Method(s)

A route can be set up to handle one or more HTTP methods (GET, PUT, POST, DELETE, etc). The route will only match a request if the pattern AND methods match.

### Creating a route

You can create a route either through the constructor:

	<?php
	use Clara\Routing\Route;
	$route = new Route('/the/pattern', 'GET', $handler);

... or through the static methods:

	<?php
	use Clara\Routing\Route;
	//This returns an object equivalent to the above example
	$route = Route::get('/the/pattern', $handler);



Using the Router
----------------

Instantiate the router:

	<?php
	use Clara\Routing\Router
	$router = new Router();

At this point you have a router with no routes. An attempt to route a request (`Clara\Http\Request`) will return `false` Until you load routes into the router.

### Loading routes from a file

Routes can be defined in a PHP file, and loaded into the router directly. The routes file MUST return an array of Route objects.

#### Example routes file


	<?php
	use Clara\Routing\Route;
	return array(
		Route::get('/foo', function(){})->setName('one'),
		Route::post('/foo', function(){})->setName('two'),
		Route::get('/foo/{variable}', function(){}),
		Route::put('/foo/bar', function(){}),
	);

The file is loaded into the router through the method `Clara\Routing\Router::importRoutesFromFile`, which takes an **absolute path** as its argument:

	<?php
	use Clara\Routing\Router;
	...
	$router->importRoutesFromFile('/full/path/to/routes/file.php');

### Adding routes manually

You can also add routes directly to the router using `Clara\Routing\Router::addRoute`:

	<?php
	...
	$router->addRoute(Route::get('/foo', $handler1));
	$router->addRoute(Route::post('/foo', $handler2));

### Routing a request

The method `Clara\Routing\Router::route` method takes a Request object (`Clara\Http\Request`), and determines if it owns any routes that are able to handle the request.

**If the router fails to find a matching route, `false` is returned.**

**If the router finds a matching route, the Route object is returned.**

