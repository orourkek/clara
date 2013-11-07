Clara\Html
==========

Helper classes for HTML related stuff.


Overview
--------

Most of the classes in this namespace (currently ALL of them, but I'll say "most" to cover my ass for the future) can be treated as strings via the `__toString()` magic method. This allows you to use the classes within to programmatically build HTML code in an object oriented fashion.


### Element (abstract class)

Abstract class representing a basic HTMl element. All classes that derive from this class support the `__toString()` magic method, which will compile the various parts of the element into an (HTML) string.


### SelfClosingElement (abstract class)

Abstract class representing a self-closing HTMl element, e.g. "<br/>"


### The `\Clara\Html\Element` Namespace

This namespace contains many classes that encapsulate many of the most common elements.


Examples
--------

### Basic Div

	<?php
	use Clara\Html\Element\Div;
	use Clara\Html\Element\P;
	use Clara\Html\Element\Strong;
	use Clara\Html\Element\Em;

	$divContent = new P(array(
		new Strong('Hello'),
		' ', //yep, you can add literal strings here too
		new Em('World!'),
	));
	$div = new Div($divContent);

	echo $div;

Will result in the following output:

	<div><p><strong>Hello</strong> <em>World!</em></p></div>


### Form Construction

	<?php
	use Clara\Html\Element\Form;
	use Clara\Html\Element\Input;
	use Clara\Html\Element\Fieldset;

	$name = new Input();
	$name->id('name')->type('text')->name('name')->placeholder('Your Name');

	$email = new Input();
	$email->id('email')->type('email')->name('email')->placeholder('Your Email');

	$fieldset = new Fieldset(array($name, $email));

	$form = new Form($fieldset);
	$form->method('POST')->action('/foo.php')->name('fooForm')->id('fooForm');

	echo $form;

Will result in the following output:

	<form method="POST" action="/foo.php" name="fooForm" id="fooForm"><fieldset><input id="name" type="text" name="name" placeholder="Your Name"/><input id="email" type="email" name="email" placeholder="Your Email"/></fieldset></form>