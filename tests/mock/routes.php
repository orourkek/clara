<?php
/**
 * routes.php
 *
 * This DocBlock was generated automatically by JetBrains PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */

use Clara\Routing\Route;

return array(
	Route::get('/foo', function(){})->setName('one'),
	Route::post('/foo', function(){})->setName('two'),
	Route::get('/foo/bar', function(){})->setName('three'),
	Route::put('/foo/bar', function(){})->setName('four'),
	//this last one is a duplicate, to be used in testing that the first encountered match is always chosen
	Route::get('/foo', function(){})->setName('five'),
);