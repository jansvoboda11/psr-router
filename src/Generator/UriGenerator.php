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
     * The URI prefix.
     *
     * @var null|string
     */
    private $prefix;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes
     * @param UriBuilder $uriBuilder
     * @param null|string $prefix
     */
    public function __construct(RouteCollection $routes, UriBuilder $uriBuilder, ?string $prefix)
    {
        $this->routes = $routes;
        $this->uriBuilder = $uriBuilder;
        $this->prefix = $prefix;
    }

    /**
     * Creates new URI generator.
     *
     * @param RouteCollection $routes
     * @param null|string $prefix
     * @return UriGenerator
     */
    public static function create(RouteCollection $routes, ?string $prefix = null): self
    {
        $uriBuilder = new UriBuilder();

        return new self($routes, $uriBuilder, $prefix);
    }

    /**
     * Generates the URI of route with given name filled with provided
     * attributes and with specified prefix.
     *
     * @param string $name
     * @param array $attributes
     * @param null|string $prefix
     * @return string
     * @throws InvalidAttribute
     * @throws RouteNotFound
     */
    public function generate(string $name, array $attributes = [], ?string $prefix = null): string
    {
        $prefix = $prefix ?? $this->prefix ?? "";

        $route = $this->routes->oneNamed($name);

        if (is_null($route)) {
            throw RouteNotFound::named($name);
        }

        $path = $route->getPath();
        $types = $route->getTypes();

        $uri = $this->uriBuilder->buildUri($path, $types, $attributes);

        return $prefix . $uri;
    }
}
