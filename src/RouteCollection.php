<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\Semantics\Validator;
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
     * Attribute type information.
     *
     * @var Types
     */
    private $types;

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
     * @param Types $types
     */
    public function __construct(RouteFactory $factory, Types $types)
    {
        $this->factory = $factory;
        $this->types = $types;
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
        $validator = new Validator();

        $factory = new RouteFactory($parser, $validator);

        return new self($factory, $types);
    }

    /**
     * Creates a GET route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function get(string $definition, RequestHandlerInterface $handler, ?string $name = null): void
    {
        $this->route("GET", $definition, $handler, $name);
    }

    /**
     * Creates a POST route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function post(string $definition, RequestHandlerInterface $handler, ?string $name = null): void
    {
        $this->route("POST", $definition, $handler, $name);
    }

    /**
     * Creates a PUT route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function put(string $definition, RequestHandlerInterface $handler, ?string $name = null): void
    {
        $this->route("PUT", $definition, $handler, $name);
    }

    /**
     * Creates a PATCH route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function patch(string $definition, RequestHandlerInterface $handler, ?string $name = null): void
    {
        $this->route("PATCH", $definition, $handler, $name);
    }

    /**
     * Creates a DELETE route.
     *
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function delete(string $definition, RequestHandlerInterface $handler, ?string $name = null): void
    {
        $this->route("DELETE", $definition, $handler, $name);
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|string $name
     * @throws InvalidRoute
     */
    public function route(string $method, string $definition, RequestHandlerInterface $handler, ?string $name): void
    {
        $route = $this->factory->create($method, $definition, $handler, $this->types);

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
