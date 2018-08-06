<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Types\Types;

/**
 * Creates parsed and validated routes.
 */
class RouteFactory
{
    /**
     * Path parser.
     *
     * @var Parser
     */
    private $parser;

    /**
     * The attribute types.
     *
     * @var Types
     */
    private $types;

    /**
     * Constructor.
     *
     * @param Parser $parser
     * @param Types $types
     */
    public function __construct(Parser $parser, Types $types)
    {
        $this->parser = $parser;
        $this->types = $types;
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param null|mixed $data
     * @return Route
     * @throws InvalidRoute
     */
    public function create(string $method, string $definition, RequestHandlerInterface $handler, $data = null): Route
    {
        if (!Method::isValid($method)) {
            throw InvalidRoute::invalidMethod($method);
        }

        $path = $this->parser->parse($definition, $this->types);

        return new Route($method, $path, $handler, $data);
    }
}
