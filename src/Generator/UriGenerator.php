<?php

declare(strict_types=1);

namespace Svoboda\Router\Generator;

use Svoboda\Router\RouteCollection;

/**
 * Generates the URI of specified route.
 */
class UriGenerator
{
    /**
     * The route collection.
     *
     * @var RouteCollection
     */
    private $routes;

    /**
     * The URI builder.
     *
     * @var UriBuilder
     */
    private $uriBuilder;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes
     * @param UriBuilder $uriBuilder
     */
    public function __construct(RouteCollection $routes, UriBuilder $uriBuilder)
    {
        $this->routes = $routes;
        $this->uriBuilder = $uriBuilder;
    }

    /**
     * Creates new URI generator.
     *
     * @param RouteCollection $routes
     * @return UriGenerator
     */
    public static function create(RouteCollection $routes): self
    {
        $uriBuilder = new UriBuilder();

        return new self($routes, $uriBuilder);
    }

    /**
     * Generates the URI of route with given name filled with provided
     * attributes and with specified prefix.
     *
     * @param string $name
     * @param array $attributes
     * @return string
     * @throws InvalidAttribute
     * @throws RouteNotFound
     */
    public function generate(string $name, array $attributes = []): string
    {
        $route = $this->routes->oneNamed($name);

        if (is_null($route)) {
            throw RouteNotFound::named($name);
        }

        $path = $route->getPath();
        $types = $route->getTypes();

        return $this->uriBuilder->buildUri($path, $types, $attributes);
    }
}
