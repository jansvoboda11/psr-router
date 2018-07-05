<?php

use Svoboda\PsrRouter\RouteCollection;

$routes = RouteCollection::create();

$routes->get("/", "HomePage", "pages.home");
$routes->get("/users/{id:num}", "UserDetail", "user.detail");

return $routes;
