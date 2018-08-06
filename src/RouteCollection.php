<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Server\RequestHandlerInterface as Handler;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Method;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\Types\InvalidTypes;
use Svoboda\Router\Types\Types;

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
     * @param null|Types $types
     * @return RouteCollection
     * @throws InvalidTypes
     */
    public static function create(?Types $types = null): self
    {
        $types = $types ?? Types::createDefault();

        $parser = new Parser();

        $factory = new RouteFactory($parser, $types);

        return new self($factory);
    }

    /**
     * Creates a GET route.
     *
     * @param string $definition
     * @param Handler $handler
     * @param null|string $name
     * @param null|mixed $data
     * @throws InvalidRoute
     */
    public function get(string $definition, Handler $handler, ?string $name = null, $data = null): void
    {
        $this->route(Method::GET, $definition, $handler, $name, $data);
    }

    /**
     * Creates a POST route.
     *
     * @param string $definition
     * @param Handler $handler
     * @param null|string $name
     * @param null|mixed $data
     * @throws InvalidRoute
     */
    public function post(string $definition, Handler $handler, ?string $name = null, $data = null): void
    {
        $this->route(Method::POST, $definition, $handler, $name, $data);
    }

    /**
     * Creates a PUT route.
     *
     * @param string $definition
     * @param Handler $handler
     * @param null|string $name
     * @param null|mixed $data
     * @throws InvalidRoute
     */
    public function put(string $definition, Handler $handler, ?string $name = null, $data = null): void
    {
        $this->route(Method::PUT, $definition, $handler, $name, $data);
    }

    /**
     * Creates a PATCH route.
     *
     * @param string $definition
     * @param Handler $handler
     * @param null|string $name
     * @param null|mixed $data
     * @throws InvalidRoute
     */
    public function patch(string $definition, Handler $handler, ?string $name = null, $data = null): void
    {
        $this->route(Method::PATCH, $definition, $handler, $name, $data);
    }

    /**
     * Creates a DELETE route.
     *
     * @param string $definition
     * @param Handler $handler
     * @param null|string $name
     * @param null|mixed $data
     * @throws InvalidRoute
     */
    public function delete(string $definition, Handler $handler, ?string $name = null, $data = null): void
    {
        $this->route(Method::DELETE, $definition, $handler, $name, $data);
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param Handler $handler
     * @param null|string $name
     * @param null|mixed $data
     * @throws InvalidRoute
     */
    public function route(string $method, string $definition, Handler $handler, ?string $name = null, $data = null): void
    {
        $route = $this->factory->create($method, $definition, $handler, $data);

        $this->routes[] = $route;

        if ($name !== null) {
            $this->named[$name] = $route;
        }
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
        if (!array_key_exists($name, $this->named)) {
            return null;
        }

        return $this->named[$name];
    }
}
