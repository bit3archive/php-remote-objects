<?php

require_once '../../../autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class rest_http_server
{
	public function run()
	{
		$context = new RequestContext();
		$context->fromRequest(Request::createFromGlobals());

		$route = new Route(
			'/rest/{method}',
			array('method' => ''),
			array('method' => '.*')
		);

		$routes = new RouteCollection();
		$routes->add('rest', $route);

		$urlMatcher = new UrlMatcher($routes, $context);


	}
}

$rest_http_server = new rest_http_server();
$rest_http_server->run();
