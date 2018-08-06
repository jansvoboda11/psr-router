<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Route\Path\RoutePath;

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
     * The handler.
     *
     * @var RequestHandlerInterface
     */
    private $handler;

    /**
     * Constructor.
     *
     * @param string $method
     * @param RoutePath $path
     * @param RequestHandlerInterface $handler
     */
    public function __construct(string $method, RoutePath $path, RequestHandlerInterface $handler)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
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
     * Returns the handler.
     *
     * @return RequestHandlerInterface
     */
    public function getHandler(): RequestHandlerInterface
    {
        return $this->handler;
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
