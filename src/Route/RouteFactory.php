<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Types\TypeCollection;

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
     * @var TypeCollection
     */
    private $types;

    /**
     * Constructor.
     *
     * @param Parser $parser
     * @param TypeCollection $types
     */
    public function __construct(Parser $parser, TypeCollection $types)
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
     * @param null|string $name
     * @param null|mixed $data
     * @return Route
     * @throws InvalidRoute
     */
    public function create(
        string $method,
        string $definition,
        RequestHandlerInterface $handler,
        ?string $name,
        $data
    ): Route {
        if (!Method::isValid($method)) {
            throw InvalidRoute::invalidMethod($method);
        }

        $path = $this->parser->parse($definition, $this->types);

        return new Route($method, $path, $handler, $name, $data);
    }
}
