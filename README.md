# Router

**Router** is a routing library (duh) for PHP built with PSR-7 in mind.

## Installation

You can install **Router** via Composer:

```
$ composer require svoboda/router
```

It only requires PHP 7.2 and the PSR-7 interfaces (the `psr/http-message`
package).

## Usage

### Registering routes

Routes can be registered in the `RouteCollection`. You have to provide the path
definition and a handler (a string, a callback or whatever you like). If you
plan to use the URI generator, you should also provide a name of the route.

```php
$routes = RouteCollection::create();

$routes->get("/login", LoginAction::class, "user.login");
$routes->post("/users/{name}", UserSettingsAction::class, "user.settings");
$routes->get("/orders[/{year:number}]", OrderListAction::class, "order.list");
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

| Type     | Pattern             |
|----------|---------------------|
| `any`    | `[^/]+`             |
| `alnum`  | `[a-zA-Z0-9]+`      |
| `alpha`  | `[a-zA-Z]+`         |
| `date`   | `\d{4}-\d{2}-\d{2}` |
| `digit`  | `\d`                |
| `number` | `\d+`               |
| `word`   | `\w+`               |

The defaults can be overridden by providing custom `Types` instance to the 
`create` method of `RouteCollection`.

#### Optional part

Optional part is a suffix of the URI that can be omitted. They can be nested
and may contain both static text and dynamic attributes. The syntax for 
optional parts is: `"[/optional]"`. 

### Routing incoming requests

After providing routes to `Router`, it can match incoming requests. The result
of the `match` method is either `null` or `Match` which contains the route
handler you specified earlier and the incoming request with filled attributes.

```php
$router = Router::create($routes);

$match = $router->match($request);

$handler = $match->getHandler();
$request = $match->getRequest();
```

### Generating route URIs

You can also create URIs from route definitions with `UriGenerator`. The 
`generate` method accepts the route name and its attributes that will be filled
in the final URI.

```php
$generator = UriGenerator::create($routes);

$uri = $generator->generate("user.settings", [
    "name" => "john.doe",
]);
```

There are few rules to keep in mind:

* All required attributes must be provided.
* When an optional attribute is provided, all preceding attributes must be
provided as well.
* Values of all provided attributes must be compatible with their types.
* All unknown attributes are ignored.

## Development

If you want to contribute to the development of **Router**, there are few
Composer commands to make it easier for you.

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
