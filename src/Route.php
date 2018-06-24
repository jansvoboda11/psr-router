<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

/**
 * Route, duh.
 */
class Route
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $handlerName;

    /**
     * @param string $method
     * @param string $path
     * @param string $handlerName
     */
    public function __construct(string $method, string $path, string $handlerName)
    {
        $this->method = $method;
        $this->path = $path;
        $this->handlerName = $handlerName;
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
     * Returns the path specification.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the name of the request handler.
     *
     * @return string
     */
    public function getHandlerName(): string
    {
        return $this->handlerName;
    }
}
