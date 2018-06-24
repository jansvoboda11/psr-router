<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use ArrayIterator;
use IteratorAggregate;

/**
 * Collection of routes.
 */
class RouteCollection implements IteratorAggregate
{
    /**
     * @var Route[]
     */
    private $routes;

    /**
     * @param Route ...$routes
     */
    public function __construct(Route ...$routes)
    {
        $this->routes = $routes;
    }

    /**
     * Creates a GET route.
     *
     * @param string $path
     * @param string $handlerName
     */
    public function get(string $path, string $handlerName): void
    {
        $this->route("GET", $path, $handlerName);
    }

    /**
     * Creates a POST route.
     *
     * @param string $path
     * @param string $handlerName
     */
    public function post(string $path, string $handlerName): void
    {
        $this->route("POST", $path, $handlerName);
    }

    /**
     * Creates a PUT route.
     *
     * @param string $path
     * @param string $handlerName
     */
    public function put(string $path, string $handlerName): void
    {
        $this->route("PUT", $path, $handlerName);
    }

    /**
     * Creates a PATCH route.
     *
     * @param string $path
     * @param string $handlerName
     */
    public function patch(string $path, string $handlerName): void
    {
        $this->route("PATCH", $path, $handlerName);
    }

    /**
     * Creates a DELETE route.
     *
     * @param string $path
     * @param string $handlerName
     */
    public function delete(string $path, string $handlerName): void
    {
        $this->route("DELETE", $path, $handlerName);
    }

    /**
     * Creates a new route.
     *
     * @param string $method
     * @param string $path
     * @param string $handlerName
     */
    public function route(string $method, string $path, string $handlerName): void
    {
        $this->routes[] = new Route($method, $path, $handlerName);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }
}
