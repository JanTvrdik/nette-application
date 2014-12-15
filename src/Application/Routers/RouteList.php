<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

namespace Nette\Application\Routers;

use Nette;


/**
 * The router broker.
 *
 * @author     David Grudl
 */
class RouteList extends Nette\Utils\ArrayList implements Nette\Application\IRouter
{
	/** @var array */
	private $cachedRoutes;


	/**
	 * Maps HTTP request to a Request object.
	 * @return Nette\Application\Request|NULL
	 */
	public function match(Nette\Http\IRequest $httpRequest)
	{
		foreach ($this as $route) {
			$appRequest = $route->match($httpRequest);
			if ($appRequest !== NULL) {
				return $appRequest;
			}
		}
		return NULL;
	}


	/**
	 * Constructs absolute URL from Request object.
	 * @return string|NULL
	 */
	public function constructUrl(Nette\Application\Request $appRequest, Nette\Http\Url $refUrl)
	{
		if ($this->cachedRoutes === NULL) {
			$routes = array();
			$routes['*'] = array();

			foreach ($this as $route) {
				$presenter = $route instanceof Route ? $route->getTargetPresenter() : NULL;

				if ($presenter === FALSE) {
					continue;
				}

				if (is_string($presenter)) {
					$presenter = strtolower($presenter);
					if (!isset($routes[$presenter])) {
						$routes[$presenter] = $routes['*'];
					}
					$routes[$presenter][] = $route;

				} else {
					foreach ($routes as $id => $foo) {
						$routes[$id][] = $route;
					}
				}
			}

			$this->cachedRoutes = $routes;
		}

		$presenter = strtolower($appRequest->getPresenterName());
		if (!isset($this->cachedRoutes[$presenter])) {
			$presenter = '*';
		}

		foreach ($this->cachedRoutes[$presenter] as $route) {
			$url = $route->constructUrl($appRequest, $refUrl);
			if ($url !== NULL) {
				return $url;
			}
		}

		return NULL;
	}


	/**
	 * Adds the router.
	 * @param  mixed
	 * @param  Nette\Application\IRouter
	 * @return void
	 */
	public function offsetSet($index, $route)
	{
		if (!$route instanceof Nette\Application\IRouter) {
			throw new Nette\InvalidArgumentException('Argument must be IRouter descendant.');
		}
		parent::offsetSet($index, $route);
		$this->cachedRoutes = NULL;
	}


	/**
	 * Removes the element at the specified position in this list.
	 * @param  int
	 * @return void
	 * @throws Nette\OutOfRangeException
	 */
	public function offsetUnset($index)
	{
		parent::offsetUnset($index);
		$this->cachedRoutes = NULL;
	}

}
