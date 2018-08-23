<?php

declare(strict_types=1);

use Svoboda\Router\RouteCollection;
use SvobodaTest\Router\Handler;

$routes = RouteCollection::create();

$routes->get("/", new Handler("HomePage"));
$routes->get("/about", new Handler("AboutPage"));
$routes->get("/contact", new Handler("ContactPage"));

$routes->post("/users", new Handler("UserCreate"));
$routes->get("/users/{id:number}", new Handler("UserRead"));
$routes->patch("/users/{id:number}", new Handler("UserUpdate"));
$routes->delete("/users/{id:number}", new Handler("UserDelete"));

$routes->post("/products", new Handler("ProductCreate"));
$routes->get("/products/{id:number}", new Handler("ProductRead"));
$routes->patch("/products/{id:number}", new Handler("ProductUpdate"));
$routes->delete("/products/{id:number}", new Handler("ProductDelete"));

$routes->post("/customers", new Handler("CustomerCreate"));
$routes->get("/customers/{id:number}", new Handler("CustomerRead"));
$routes->patch("/customers/{id:number}", new Handler("CustomerUpdate"));
$routes->delete("/customers/{id:number}", new Handler("CustomerDelete"));

$routes->post("/categories", new Handler("CategoryCreate"));
$routes->get("/categories/{id:number}", new Handler("CategoryRead"));
$routes->patch("/categories/{id:number}", new Handler("CategoryUpdate"));
$routes->delete("/categories/{id:number}", new Handler("CategoryDelete"));

$routes->post("/orders", new Handler("OrderCreate"));
$routes->get("/orders/{id:number}", new Handler("OrderRead"));
$routes->patch("/orders/{id:number}", new Handler("OrderUpdate"));
$routes->delete("/orders/{id:number}", new Handler("OrderDelete"));

return $routes;
