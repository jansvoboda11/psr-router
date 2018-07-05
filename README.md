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

```php
$routes = RouteCollection::create();

$routes->get("/", HomeAction::class, "pages.home");
$routes->post("/users/{name}", UserSettingsAction::class, "user.settings");
$routes->get("/orders[/{year:num}]", OrderListAction::class, "order.list");
```

The path definition can contain three types of fragments.

#### Static text

Static text describes a part of the URI that is always present and never
changes between requests. For example, `"/login"` is a path definition
containing only static text.

#### Dynamic attribute

Dynamic attribute is a part of the URI that can differ from request to request.
The dynamic attribute has a type associated with it (for example number, date 
or text). Its value is captured by the router and added to the request under 
the attribute name. The basic syntax for dynamic attributes is `"{name:type}"`.
The type can be omitted and it defaults to `any`. That means `"{name}"` is a
shorthand for `"{name:any}"`.

The library contains few built-in attribute types:

| Type  | Pattern |
|-------|---------|
| `any` | `[^/]+` |
| `num` | `\d+`   |

The defaults can be overridden by providing custom `Context` to the `create` 
method of `Router` and `UriGenerator`.

#### Optional part

Optional part is a suffix of the URI that can be omitted. They can be nested
and can contain both static text and dynamic attributes. The syntax for 
optional parts is: `"[/optional]"`. 

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
