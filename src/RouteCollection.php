<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Route;
use Svoboda\PsrRouter\Route\RouteFactory;

/**
 * Collection of routes.
 */
class RouteCollection
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
     * @param string $definition
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function get(string $definition, $handler): void
    {
        $this->route("GET", $definition, $handler);
    }

    /**
     * Creates a POST route.
     *
     * @param string $definition
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function post(string $definition, $handler): void
    {
        $this->route("POST", $definition, $handler);
    }

    /**
     * Creates a PUT route.
     *
     * @param string $definition
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function put(string $definition, $handler): void
    {
        $this->route("PUT", $definition, $handler);
    }

    /**
     * Creates a PATCH route.
     *
     * @param string $definition
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function patch(string $definition, $handler): void
    {
        $this->route("PATCH", $definition, $handler);
    }

    /**
     * Creates a DELETE route.
     *
     * @param string $definition
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function delete(string $definition, $handler): void
    {
        $this->route("DELETE", $definition, $handler);
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param mixed $handler
     * @throws InvalidRoute
     */
    public function route(string $method, string $definition, $handler): void
    {
        $this->routes[] = $this->factory->createRoute($method, $definition, $handler);
    }

    /**
     * Returns all registered routes.
     *
     * @return Route[]
     */
    public function all(): array
    {
        return $this->routes;
    }
}
