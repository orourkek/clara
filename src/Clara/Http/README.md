Clara\Http
==========

This code encapsulates many parts of the HTTP specification into a set of easy-to-use classes.


Uri
---

The Uri class (`Clara\Http\Uri`) is an object representation of a typical HTTP URI, with the following components:

* scheme
* user
* pass
* host
* port
* path
* query
* fragment

Which are taken directly from a typical URI string:

	{scheme}://{user}:{pass}@{host}:{port}/{path}?{query}#{fragment}

### Creating a Uri object

The Uri constructor takes a URI string as its argument:

	<?php
	use Clara\Http\Uri;
	$uri = new Uri('http://example.com/foo?bar=baz#taz');

which would result in the following property values:

 Property    | Value
-------------|--------------
 scheme      | http
 user        |
 pass        |
 host        | example.com
 port        |
 path        | /foo
 query       | bar=baz
 fragment    | #taz

### `__toString`

A Uri object can be used as a string through its `__toString` magic method, which reconstitutes the URI string from the seperate components.

	<?php
	use Clara\Http\Uri;
	$uriString = 'http://example.com/foo?bar=baz#taz';
	$uri = new Uri($uriString);
	$result = $uriString === (string) $uri; //bool(TRUE)



Message
-------

This abstract class represents a generic HTTP message, and contains properties that are common to all HTTP messages (requests/responses):

* protocol [string]
* headers [array]
* body [string]



Request
-------

This class encapsulates a request received by the application.

*Note: This class extends `Clara\Http\Message`*

A Request object has the following properties:

* *all properties of Clara\Http\Message*
* method [string]
* uri [Clara\Http\Uri]

### Creating a Request object directly

The request class does have a defined constructor, so instantiating a Request will leave all properties empty.

### Creating a Request object using the apache environment

A Request object can be generated with the necessary data pulled directly from the environment (via `$_SERVER`) through the static method `Clara\Http\Request::createFromEnvironment`

	<?php
	use Clara\Http\Request;
	$request = Request::createFromEnvironment();

**NOTE: Because this method uses `$_SERVER` as a source for its information, be careful that you use appropriate caution in ensuring `$_SERVER` actually has data to be pulled, e.g. in a CLI environment**



Response
--------

This class is used to construct and send a response back to the entity that made the initial request.

*Note: This class extends `Clara\Http\Message`*

A Response object has the following properties:

* *all properties of Clara\Http\Message*
* statusCode [int]

### Additional Properties

The Response class also contains some helper properties/constants:

#### `Clara\Http\Response::$statusTexts`

This is an array containing all possible HTTP status texts, indexed by the status code. These messages are accessed statically:

	<?php
	use Clara\Http\Response;
	$text = Response::$statusTexts[$statusCode];

	//or, from within object context:
	...
	$text = self::$statusTexts[$statusCode];

#### Status code constants

This class also contains convenience constants for every HTTP status code. A few examples are reproduced here:

	<?php
	...
	const HTTP_CONTINUE = 100;
	const HTTP_SWITCHING_PROTOCOLS = 101;
	const HTTP_PROCESSING = 102;            // RFC2518
	const HTTP_OK = 200;
	...

### Creating a Response object

The constructor takes three arguments. The signature is as follows:

	<?php
	...
	public function __construct($content='', $status=200, $headers=array()) { ... }

### Sending a Response

Once created, the Response can be sent using the method `Clara\Http\Response::send`. This method sends the headers assigned to the Response object, then sends the body of the message.

### `__toString`

A Response object can be used as a string through its `__toString` magic method, which converts the Response into a raw HTTP message string:

	<?php
	use Clara\Http\Response;
	$response = new Response();
	$response->setBody('this is the body');
	$response->setStatusCode(200);
	$response->setHeader('Foo', 'Bar');
	$response->setHeader('Baz', 'Taz');

	echo $response;

... will output something like:

	HTTP/1.1 200 OK
	Foo: Bar
	Baz: Taz
	this is the body

