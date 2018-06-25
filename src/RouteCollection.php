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
     * @var RouteFactory
     */
    private $factory;

    /**
     * @var Route[]
     */
    private $routes;

    /**
     * @param RouteFactory $factory
     */
    public function __construct(?RouteFactory $factory = null)
    {
        $this->factory = $factory ?? new RouteFactory();
        $this->routes = [];
    }

    /**
     * Creates a GET route.
     *
     * @param string $path
     * @param string $handlerName
     * @throws InvalidRoute
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
     * @throws InvalidRoute
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
     * @throws InvalidRoute
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
     * @throws InvalidRoute
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
     * @throws InvalidRoute
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
     * @throws InvalidRoute
     */
    public function route(string $method, string $path, string $handlerName): void
    {
        $this->routes[] = $this->factory->createRoute($method, $path, $handlerName);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }
}
