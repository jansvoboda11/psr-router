<?php

declare(strict_types=1);

use Svoboda\Router\RouteCollection;
use SvobodaTest\Router\FakeHandler;

$routes = RouteCollection::create();

$routes->get("/", new FakeHandler());
$routes->get("/about", new FakeHandler());
$routes->get("/contact", new FakeHandler());

$routes->post("/users", new FakeHandler());
$routes->get("/users/{id:number}", new FakeHandler());
$routes->patch("/users/{id:number}", new FakeHandler());
$routes->delete("/users/{id:number}", new FakeHandler());

$routes->post("/products", new FakeHandler());
$routes->get("/products/{id:number}", new FakeHandler());
$routes->patch("/products/{id:number}", new FakeHandler());
$routes->delete("/products/{id:number}", new FakeHandler());

$routes->post("/customers", new FakeHandler());
$routes->get("/customers/{id:number}", new FakeHandler());
$routes->patch("/customers/{id:number}", new FakeHandler());
$routes->delete("/customers/{id:number}", new FakeHandler());

$routes->post("/categories", new FakeHandler());
$routes->get("/categories/{id:number}", new FakeHandler());
$routes->patch("/categories/{id:number}", new FakeHandler());
$routes->delete("/categories/{id:number}", new FakeHandler());

$routes->post("/orders", new FakeHandler());
$routes->get("/orders/{id:number}", new FakeHandler());
$routes->patch("/orders/{id:number}", new FakeHandler());
$routes->delete("/orders/{id:number}", new FakeHandler());

return $routes;
