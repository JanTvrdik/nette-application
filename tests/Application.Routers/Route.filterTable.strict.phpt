<?php

/**
 * Test: Nette\Application\Routers\Route with FilterTable
 */

use Nette\Application\Routers\Route,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/Route.inc';


Route::addStyle('#xlat', 'presenter');
Route::setStyleProperty('#xlat', Route::FILTER_TABLE, array(
	'produkt' => 'Product',
	'kategorie' => 'Category',
	'zakaznik' => 'Customer',
	'kosik' => 'Basket',
));
Route::setStyleProperty('#xlat', Route::FILTER_STRICT, TRUE);

$route = new Route('<presenter #xlat>', array());

testRouteIn($route, '/kategorie/', 'Category', array(
	'test' => 'testvalue',
), '/kategorie?test=testvalue');

testRouteIn($route, '/other/');
