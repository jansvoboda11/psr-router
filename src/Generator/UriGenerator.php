<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Generator;
use Svoboda\PsrRouter\Compiler\Context;
use Svoboda\PsrRouter\RouteCollection;

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
     * The prefix placed on the beginning each URI.
     *
     * @var string
     */
    private $prefix;

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
     * @param null|string $prefix
     */
    public function __construct(RouteCollection $routes, UriBuilder $uriBuilder, ?string $prefix = null)
    {
        $this->routes = $routes;
        $this->uriBuilder = $uriBuilder;
        $this->prefix = $prefix ?? "";
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
     * @param string $prefix
     * @return string
     * @throws InvalidAttribute
     * @throws RouteNotFound
     */
    public function generate(string $name, array $attributes = [], ?string $prefix = null): string
    {
        // todo: refactor away
        $context = Context::createDefault();

        $prefix = $prefix ?? $this->prefix;

        $route = $this->routes->oneNamed($name);

        if (is_null($route)) {
            throw new RouteNotFound("Route does not exist.");
        }

        $uri = $this->uriBuilder->buildUri($route->getPath(), $attributes, $context);

        return $prefix . $uri;
    }
}
