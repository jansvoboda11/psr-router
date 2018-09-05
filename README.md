# Router

**Router** is a PHP routing library built with [PSR-7](https://www.php-fig.org/psr/psr-7/), [PSR-15](https://www.php-fig.org/psr/psr-15/) and [PSR-17](https://www.php-fig.org/psr/psr-17/) in mind.

Routing libraries, in general, allow your application to execute different code paths based on the structure of incoming HTTP requests, usually their URI.

This library provides a simple interface for handling PSR-7 requests with PSR-15 middleware and handlers.

## Installation

You can install **Router** via Composer:

```
$ composer require svoboda/router
```

The only requirements are PHP 7.2 and few PSR packages.

## Usage

The API is designed to be really intuitive.
Most of the time, you will interact with the `RouteCollection` class where you will register your routes.
The `Router` class matches incoming HTTP requests against defined routes and using `UriGenerator` you can create URIs pointing to routes.

### Registering routes

You should register your routes in the `RouteCollection`.
You have to provide the path definition and a handler.
If you plan to use the URI generator, you should also provide a name of the route.
You can also add any data to the route with the fourth argument, which can be accessed later on (e.g. in your custom middleware).

```php
$routes = RouteCollection::create();

$routes->get("/login", new LoginHandler());
$routes->post("/users/{name}", new UserHandler(), "user");
$routes->get("/orders[/{year:number}]", new OrdersHandler(), "orders", ["auth" => true]);
```

Parts of the definition can be divided into three categories.

#### Static text

A static text describes a part of the URI that never changes between requests.
For example `/login` and `/users/` are static parts of the definitions above.

#### Dynamic attribute

A dynamic attribute is a part of the URI that can differ from request to request.
The dynamic attribute has a name and a type associated with it (for example number, date or text).
Its value is captured by the router and added to the request under the attribute name.

The basic syntax for defining dynamic attributes is `{name:type}`.
The type can be omitted and defaults to `any`, which means that `{name}` is a shorthand for `{name:any}`.

**Router** contains few built-in attribute types:

* `any` - all characters except `/`
* `alnum` - alphanumeric characters
* `alpha` - characters of the English alphabet (both lowercase and uppercase)
* `date` - date in the `yyyy-mm-dd` format
* `digit` - single decimal digit
* `number` - integer
* `word` - alphanumeric characters including `_`

The defaults can be overridden by providing custom `Types` instance when creating `RouteCollection`.

#### Optional part

An optional part is a suffix of the URI that may not be present in some requests.
Optional parts may contain both static text and dynamic attributes and can be nested.
To declare part of the definition as optional, put it in square brackets: `[/{year:number}]`.

### Matching incoming requests

The `Router` class tries to match incoming requests to definitions of your routes.
Its `match` method accepts instances of `ServerRequestInterface` and returns a `Match` object if the request matches any route definition.
The result contains the matched route and modified request filled with request URI attributes.

If the incoming request does not match any route definition, the method throws a `Failure` exception.
The exception holds the original request and an array of routes that would match the request if a different HTTP method was used (this is useful for `AutomaticOptionsMiddleware`).

```php
/** @var ServerRequestInterface $request */

$router = Router::create($routes);

try {
    $match = $router->match($request);
    $route = $match->getRoute();
    $handler = $route->getHandler();
    $request = $match->getRequest();
} catch (Failure $failure) {
    $routes = $failure->getUriRoutes();
    $request = $failure->getRequest();
}
```

#### Using built-in middleware

The library also provides five middleware that take care of few things for you. You should use them in following order:

1. `RouteMatchingMiddleware` tries to match the request and populates it with either `Match` or `Failure` attribute.
2. `AutomaticOptionsMiddleware` responds to OPTIONS requests with a list of allowed methods for the requested URI.
3. `AutomaticHeadMiddleware` responds to HEAD requests according to [the specification](https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/HEAD) if the GET route exists.
4. `MethodNotAllowedMiddleware` responds with a 405 status to requests using an invalid method with a valid URI.
5. `RouteDispatchingMiddleware` dispatches the request to the matched handler and returns its response. 

### Generating route URIs

The **Router** library is also able to generate URIs from route specifications.
This process is sometimes called *reverse routing* and it can be  useful when you want to create links to your routes in a declarative way.

After creating an instance of `UriGenerator` with a route collection, you can use its `generate` method.
It accepts the route name, the attributes that will be filled in and outputs a complete URI.

```php
$generator = UriGenerator::create($routes);

$uri = $generator->generate("user", [
    "name" => "john.doe",
]);
```

There are few rules to keep in mind:

* All required attributes must be provided.
* When an optional attribute is provided, all preceding attributes must be provided as well.
* Values of all provided attributes must be compatible with their types.
* All unknown attributes are ignored.

## Development

There are few commands that make developing **Router** a little bit easier:

* `$ ./bin/test` - run automated tests (using [PHPUnit](https://github.com/sebastianbergmann/phpunit))
* `$ ./bin/analyse` - run static analysis (using [PHPStan](https://github.com/phpstan/phpstan))
* `$ ./bin/bench` - run benchmarks (using [PHPBench](https://github.com/phpbench/phpbench))
