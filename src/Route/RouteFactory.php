<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

use Psr\Http\Server\MiddlewareInterface;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Semantics\Validator;
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
     * Route validator.
     *
     * @var Validator
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param Parser $parser
     * @param Validator $validator
     */
    public function __construct(Parser $parser, Validator $validator)
    {
        $this->parser = $parser;
        $this->validator = $validator;
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param MiddlewareInterface $middleware
     * @param Types $types
     * @return Route
     * @throws InvalidRoute
     */
    public function create(string $method, string $definition, MiddlewareInterface $middleware, Types $types): Route
    {
        $path = $this->parser->parse($definition);

        $this->validator->validate($path, $types);

        return new Route($method, $path, $middleware, $types);
    }
}
