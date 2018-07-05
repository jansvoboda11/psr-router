# psr-router

Simple router built on top of PSR-7.

## Installation

You can install psr-router via Composer.

```
$ composer require svoboda/psr-router
```

## Usage

### Define your routes:

```php
$routes = new RouteCollection();

$routes->get("/", HomeAction::class, "pages.home");
$routes->get("/users/{name}", UserDetailsAction::class, "user.details");
```

### Route an incoming HTTP request:

```php
$router = Router::create($routes);

$match = $router->match($request);

$handler = $match->getHandler();
$request = $match->getRequest();
```

### Generate the URI of a route:

```php
$generator = UriGenerator::create($routes);

$uri = $generator->generate("user.details", [
    "name" => "john.doe",
]);
```
