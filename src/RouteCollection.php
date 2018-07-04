<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Route;
use Svoboda\PsrRouter\Route\RouteFactory;
use Svoboda\PsrRouter\Semantics\Validator;

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
     * Constructor.
     *
     * @param RouteFactory $factory
     */
    public function __construct(?RouteFactory $factory = null)
    {
        $this->factory = $factory ?? new RouteFactory(new Parser(), new Validator());
        $this->routes = [];
    }

    /**
     * Creates a GET route.
     *
     * @param string $definition
     * @param mixed $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function get(string $definition, $handler, ?string $name = null): void
    {
        $this->route("GET", $definition, $handler, $name);
    }

    /**
     * Creates a POST route.
     *
     * @param string $definition
     * @param mixed $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function post(string $definition, $handler, ?string $name = null): void
    {
        $this->route("POST", $definition, $handler, $name);
    }

    /**
     * Creates a PUT route.
     *
     * @param string $definition
     * @param mixed $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function put(string $definition, $handler, ?string $name = null): void
    {
        $this->route("PUT", $definition, $handler, $name);
    }

    /**
     * Creates a PATCH route.
     *
     * @param string $definition
     * @param mixed $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function patch(string $definition, $handler, ?string $name = null): void
    {
        $this->route("PATCH", $definition, $handler, $name);
    }

    /**
     * Creates a DELETE route.
     *
     * @param string $definition
     * @param mixed $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function delete(string $definition, $handler, ?string $name = null): void
    {
        $this->route("DELETE", $definition, $handler, $name);
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param mixed $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function route(string $method, string $definition, $handler, ?string $name = null): void
    {
        $this->routes[] = $this->factory->createRoute($method, $definition, $handler, $name);
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

    /**
     * Returns one route with the given name.
     *
     * @param string $name
     * @return null|Route
     */
    public function oneNamed(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }
}
