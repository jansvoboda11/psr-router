<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route;

use Svoboda\PsrRouter\Route\Path\RoutePath;

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
     * @var mixed
     */
    private $handler;

    /**
     * The optional name.
     *
     * @var null|string
     */
    private $name;

    /**
     * Constructor.
     *
     * @param string $method
     * @param RoutePath $path
     * @param mixed $handler
     * @param null|string $name
     */
    public function __construct(string $method, RoutePath $path, $handler, ?string $name = null)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handler = $handler;
        $this->name = $name;
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
     * @return mixed
     */
    public function getHandler()
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
     * Rebuilds the route definition.
     *
     * @return string
     */
    public function rebuildDefinition(): string
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
