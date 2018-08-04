<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

use Psr\Http\Server\RequestHandlerInterface;
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
     * @param RequestHandlerInterface $handler
     * @param Types $types
     * @return Route
     * @throws InvalidRoute
     */
    public function create(string $method, string $definition, RequestHandlerInterface $handler, Types $types): Route
    {
        $path = $this->parser->parse($definition);

        $this->validator->validate($path, $types);

        return new Route($method, $path, $handler, $types);
    }
}
