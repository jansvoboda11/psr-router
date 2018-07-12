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
     * @var UriFactory
     */
    private $uriFactory;

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
     * @param UriFactory $uriFactory
     * @param null|string $prefix
     */
    public function __construct(RouteCollection $routes, UriFactory $uriFactory, ?string $prefix)
    {
        $this->routes = $routes;
        $this->uriFactory = $uriFactory;
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
        $uriFactory = new UriFactory();

        return new self($routes, $uriFactory, $prefix);
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

        if ($route === null) {
            throw RouteNotFound::named($name);
        }

        $path = $route->getPath();
        $types = $route->getTypes();

        $uri = $this->uriFactory->create($path, $types, $attributes);

        return $prefix . $uri;
    }
}
