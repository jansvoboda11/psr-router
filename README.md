# psr-router

Simple router built on top of PSR-7.

## Installation

You can install psr-router via Composer.

```
$ composer require svoboda/psr-router
```

## Usage

### Registering routes

Routes can be registered in the `RouteCollection`. You have to provide the path
definition and a handler (a string, a callback or whatever you like). If you
plan to use the URI generator, you should also provide a name.

The path definition can contain three types of fragments:

* static text: `/users`
* dynamic attributes: `{name:type}` or `{name}` (implies `any` type)
* optional parts: `/users[/all]` (can only appear at the very end of
definitions and can be nested)

The built-in attribute types are the following:

| Name  | Pattern |
|-------|---------|
| `any` | `[^/]+` |
| `num` | `\d+`   |

An example route collection:

```php
$routes = RouteCollection::create();

$routes->get("/", HomeAction::class, "pages.home");
$routes->post("/users/{name}", UserSettingsAction::class, "user.settings");
$routes->get("/orders[/{year:num}]", OrderListAction::class, "order.list");
```

### Routing incoming requests

After defining routes, `Router` can match incoming requests (instances of PSR-7
`ServerRequestInterface` interface). Result of the `match` method can be 
either `null` or `Match`, which contains the route handler and a request with 
filled route attributes.

```php
$router = Router::create($routes);

$match = $router->match($request);

$handler = $match->getHandler();
$request = $match->getRequest();
```

### Generating route URIs

Generating URIs from route definitions is also possible. The `UriGenerator`
takes the name of a route, an array of attributes and returns a complete URI
filled with attributes. It requires the following:

* all required attributes are provided
* if an optional attribute is provided, all preceding attributes are provided
too, even if they are optional
* all provided attributes have the correct format

If you provide an attribute that is not part of the route definition, it is 
ignored.

```php
$generator = UriGenerator::create($routes);

$uri = $generator->generate("user.settings", [
    "name" => "john.doe",
]);
```
