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
     * Constructor.
     *
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param RequestHandlerInterface $handler
     * @param Types $types
     * @return Route
     * @throws InvalidRoute
     */
    public function create(string $method, string $definition, RequestHandlerInterface $handler, Types $types): Route
    {
        if (!Method::isValid($method)) {
            throw InvalidRoute::invalidMethod($method);
        }

        $path = $this->parser->parse($definition, $types);

        return new Route($method, $path, $handler);
    }
}
