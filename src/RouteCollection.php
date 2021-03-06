<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Method;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\Types\InvalidTypes;
use Svoboda\Router\Types\TypeCollection;

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
     * All routes.
     *
     * @var Route[]
     */
    private $routes;

    /**
     * Named routes.
     *
     * @var Route[]
     */
    private $named;

    /**
     * Constructor.
     *
     * @param RouteFactory $factory
     */
    public function __construct(RouteFactory $factory)
    {
        $this->factory = $factory;
        $this->routes = [];
        $this->named = [];
    }

    /**
     * Creates new empty route collection.
     *
     * @param null|TypeCollection $types
     * @return RouteCollection
     * @throws InvalidTypes
     */
    public static function create(?TypeCollection $types = null): self
    {
        $types = $types ?? TypeCollection::createDefault();

        $parser = new Parser();

        $factory = new RouteFactory($parser, $types);

        return new self($factory);
    }

    /**
     * Creates a GET route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @param null|mixed $data
     * @return Route
     * @throws InvalidRoute
     */
    public function get(
        string $definition,
        RequestHandlerInterface $handler,
        ?string $name = null,
        $data = null
    ): Route {
        return $this->route(Method::GET, $definition, $handler, $name, $data);
    }

    /**
     * Creates a POST route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @param null|mixed $data
     * @return Route
     * @throws InvalidRoute
     */
    public function post(
        string $definition,
        RequestHandlerInterface $handler,
        ?string $name = null,
        $data = null
    ): Route {
        return $this->route(Method::POST, $definition, $handler, $name, $data);
    }

    /**
     * Creates a PUT route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @param null|mixed $data
     * @return Route
     * @throws InvalidRoute
     */
    public function put(
        string $definition,
        RequestHandlerInterface $handler,
        ?string $name = null,
        $data = null
    ): Route {
        return $this->route(Method::PUT, $definition, $handler, $name, $data);
    }

    /**
     * Creates a PATCH route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @param null|mixed $data
     * @return Route
     * @throws InvalidRoute
     */
    public function patch(
        string $definition,
        RequestHandlerInterface $handler,
        ?string $name = null,
        $data = null
    ): Route {
        return $this->route(Method::PATCH, $definition, $handler, $name, $data);
    }

    /**
     * Creates a DELETE route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @param null|mixed $data
     * @return Route
     * @throws InvalidRoute
     */
    public function delete(
        string $definition,
        RequestHandlerInterface $handler,
        ?string $name = null,
        $data = null
    ): Route {
        return $this->route(Method::DELETE, $definition, $handler, $name, $data);
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @param null|mixed $data
     * @return Route
     * @throws InvalidRoute
     */
    public function route(
        string $method,
        string $definition,
        RequestHandlerInterface $handler,
        ?string $name = null,
        $data = null
    ): Route {
        $route = $this->factory->create($method, $definition, $handler, $name, $data);

        $this->routes[] = $route;

        if ($name !== null) {
            $this->named[$name] = $route;
        }

        return $route;
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
     * Returns the number of registered routes.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->routes);
    }

    /**
     * Returns one route with the given name.
     *
     * @param string $name
     * @return null|Route
     */
    public function oneNamed(string $name): ?Route
    {
        if (!array_key_exists($name, $this->named)) {
            return null;
        }

        return $this->named[$name];
    }
}
