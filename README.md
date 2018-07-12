# Router

**Router** is a PHP routing library built with [PSR-7](https://www.php-fig.org/psr/psr-7/) in mind.

Routing libraries in general allow your application to execute different code paths based on the structure of incoming HTTP requests, most commonly their URI.

This library features simple declarative way of creating routes, has great performance and is highly flexible.

## Installation

You can install **Router** via Composer:

```
$ composer require svoboda/router
```

It only requires PHP 7.2 and the PSR-7 interfaces (the `psr/http-message` package).

## Usage

### Registering routes

You should register your routes in the `RouteCollection`.
You have to provide the path definition and a handler (a string, a callback or whatever you like).
If you plan to use the URI generator, you should also provide a name of the route.

```php
$routes = RouteCollection::create();

$routes->get("/login", LoginAction::class, "user.login");
$routes->post("/users/{name}", UserSettingsAction::class, "user.settings");
$routes->get("/orders[/{year:number}]", OrderListAction::class, "order.list");
```

Parts of the definition can be divided into three categories.

#### Static text

A static text describes a part of the URI that never changes between requests.
For example, `/login` and `/users/` are parts of the definition containing only static text.

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
Optional parts can be nested and contain both static text and dynamic attributes.
To declare a part of the definition optional, enclose it in square brackets: `[/{year:number}]`.

### Routing incoming requests

The `Router` class processes incoming requests based on the route collection.
Its `match` method accepts instance of the `ServerRequestInterface` and returns `Match` if the request matches any route definition. 
The result contains the route handler and modified request with filled route attributes.

```php
$router = Router::create($routes);

$match = $router->match($request);

$handler = $match->getHandler();
$request = $match->getRequest();
```

### Generating route URIs

The **Router** library is also able to generate URIs from route specifications.
This process is sometimes called *reverse routing* and it can be  useful when you want to dynamically create links in a declarative way.

After creating an instance of `UriGenerator` with a route collection, you can use its `generate` method.
It accepts the route name, the attributes that will be filled in and outputs a complete URI.

```php
$generator = UriGenerator::create($routes);

$uri = $generator->generate("user.settings", [
    "name" => "john.doe",
]);
```

There are few rules to keep in mind:

* All required attributes must be provided.
* When an optional attribute is provided, all preceding attributes must be provided as well.
* Values of all provided attributes must be compatible with their types.
* All unknown attributes are ignored.

## Development

There are few Composer commands that make developing **Router** a little bit easier.

Run automated test (using [PHPUnit](https://github.com/sebastianbergmann/phpunit)):

```
$ composer test
```

Run static analysis (using [PHPStan](https://github.com/phpstan/phpstan)):

```
$ composer analyse
```

Run benchmarks (using [PHPBench](https://github.com/phpbench/phpbench)):

```
$ composer bench
```
