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
     * @param null|Context $context
     * @return UriGenerator
     */
    public static function create(RouteCollection $routes, ?Context $context): self
    {
        $context = $context ?? Context::createDefault();

        $uriBuilder = new UriBuilder($context);

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

        return $this->uriBuilder->buildUri($route->getPath(), $attributes);
    }
}
