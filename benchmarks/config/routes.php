<?php

declare(strict_types=1);

use Svoboda\Router\RouteCollection;
use SvobodaTest\Router\Handler;

$routes = RouteCollection::create();

$routes->get("/", new Handler("HomePage"), "pages.home");
$routes->get("/users/{id:number}", new Handler("UserDetail"), "user.detail");

return $routes;
