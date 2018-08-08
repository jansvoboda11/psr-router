<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

use Psr\Http\Server\RequestHandlerInterface as Handler;
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
     * @var Handler
     */
    private $handler;

    /**
     * The name.
     *
     * @var null|string
     */
    private $name;

    /**
     * Data associated with the route.
     *
     * @var null|mixed
     */
    private $data;

    /**
     * Constructor.
     *
     * @param string $method
     * @param RoutePath $path
     * @param Handler $handler
     * @param null|string $name
     * @param null|mixed $data
     */
    public function __construct(string $method, RoutePath $path, Handler $handler, ?string $name = null, $data = null)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
        $this->name = $name;
        $this->data = $data;
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
     * @return Handler
     */
    public function getHandler(): Handler
    {
        return $this->handler;
    }

    /**
     * Returns the name.
     *
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Returns the data.
     *
     * @return null|mixed
     */
    public function getData()
    {
        return $this->data;
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
