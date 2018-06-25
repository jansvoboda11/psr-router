<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route;

use Svoboda\PsrRouter\Route\Parts\RoutePart;

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
     * @var RoutePart
     */
    private $ast;

    /**
     * @var mixed
     */
    private $handler;

    /**
     * @param string $method
     * @param RoutePart $ast
     * @param mixed $handler
     */
    public function __construct(string $method, RoutePart $ast, $handler)
    {
        $this->method = $method;
        $this->ast = $ast;
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
     * Returns the top route part.
     *
     * @return RoutePart
     */
    public function getAst(): RoutePart
    {
        return $this->ast;
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
     * Rebuilds the route definition.
     *
     * @return string
     */
    public function rebuildDefinition(): string
    {
        return $this->ast->getDefinition();
    }

    /**
     * Gathers all route attributes.
     *
     * @return array
     */
    public function gatherAttributes(): array
    {
        return $this->ast->getAttributes();
    }
}
