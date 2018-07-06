<?php

use Svoboda\Router\RouteCollection;

$routes = RouteCollection::create();

$routes->get("/", "HomePage", "pages.home");
$routes->get("/users/{id:number}", "UserDetail", "user.detail");

return $routes;
