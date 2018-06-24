<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser;

use Svoboda\PsrRouter\InvalidRoute;
use Svoboda\PsrRouter\Parser\Parts\RoutePart;

/**
 * Loss-less representation of the original route definition.
 */
class ParsedRoute
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
     * @var string
     */
    private $handlerName;

    /**
     * @param string $method
     * @param RoutePart $ast
     * @param string $handlerName
     */
    public function __construct(string $method, RoutePart $ast, string $handlerName)
    {
        $this->method = $method;
        $this->ast = $ast;
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
     * Returns the top route part.
     *
     * @return RoutePart
     */
    public function getAst(): RoutePart
    {
        return $this->ast;
    }

    /**
     * Returns the handler name.
     *
     * @return string
     */
    public function getHandlerName(): string
    {
        return $this->handlerName;
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
     * @throws InvalidRoute
     */
    public function gatherAttributes(): array
    {
        return $this->ast->getAttributes();
    }
}
