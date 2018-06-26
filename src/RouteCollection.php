<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use ArrayIterator;
use IteratorAggregate;
use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Route;
use Svoboda\PsrRouter\Route\RouteFactory;

/**
 * Collection of routes.
 */
class RouteCollection implements IteratorAggregate
{
    /**
     * The factory for creating new routes.
     *
     * @var RouteFactory
     */
    private $factory;

    /**
     * The routes.
     *
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
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function get(string $path, $handler): void
    {
        $this->route("GET", $path, $handler);
    }

    /**
     * Creates a POST route.
     *
     * @param string $path
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function post(string $path, $handler): void
    {
        $this->route("POST", $path, $handler);
    }

    /**
     * Creates a PUT route.
     *
     * @param string $path
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function put(string $path, $handler): void
    {
        $this->route("PUT", $path, $handler);
    }

    /**
     * Creates a PATCH route.
     *
     * @param string $path
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function patch(string $path, $handler): void
    {
        $this->route("PATCH", $path, $handler);
    }

    /**
     * Creates a DELETE route.
     *
     * @param string $path
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function delete(string $path, $handler): void
    {
        $this->route("DELETE", $path, $handler);
    }

    /**
     * Creates a new route.
     *
     * @param string $method
     * @param string $path
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function route(string $method, string $path, $handler): void
    {
        $this->routes[] = $this->factory->createRoute($method, $path, $handler);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }
}
