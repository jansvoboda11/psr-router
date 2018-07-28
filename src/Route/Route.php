<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

use Psr\Http\Server\MiddlewareInterface;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Types\Types;

/**
 * Route, duh.
 */
class Route
{
    /**
     * The HTTP method.
     *
     * @var string
     */
    private $method;

    /**
     * The path definition.
     *
     * @var RoutePath
     */
    private $path;

    /**
     * The middleware.
     *
     * @var MiddlewareInterface
     */
    private $middleware;

    /**
     * Attribute types.
     *
     * @var Types
     */
    private $types;

    /**
     * Constructor.
     *
     * @param string $method
     * @param RoutePath $path
     * @param MiddlewareInterface $middleware
     * @param Types $types
     */
    public function __construct(string $method, RoutePath $path, MiddlewareInterface $middleware, Types $types)
    {
        $this->method = $method;
        $this->path = $path;
        $this->middleware = $middleware;
        $this->types = $types;
    }

    /**
     * Returns the HTTP method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the route path.
     *
     * @return RoutePath
     */
    public function getPath(): RoutePath
    {
        return $this->path;
    }

    /**
     * Returns the middleware.
     *
     * @return MiddlewareInterface
     */
    public function getMiddleware(): MiddlewareInterface
    {
        return $this->middleware;
    }

    /**
     * Returns the attribute types.
     *
     * @return Types
     */
    public function getTypes(): Types
    {
        return $this->types;
    }

    /**
     * Returns the route definition.
     *
     * @return string
     */
    public function getDefinition(): string
    {
        return $this->path->getDefinition();
    }

    /**
     * Returns all route attributes.
     *
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->path->getAttributes();
    }
}
