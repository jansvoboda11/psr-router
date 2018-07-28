<?php

declare(strict_types=1);

use Svoboda\Router\RouteCollection;
use SvobodaTest\Router\Middleware;

$routes = RouteCollection::create();

$routes->get("/", new Middleware("HomePage"), "pages.home");
$routes->get("/users/{id:number}", new Middleware("UserDetail"), "user.detail");

return $routes;
