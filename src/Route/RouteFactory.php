<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route;

use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\Semantics\Validator;

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
     * @param mixed $handler
     * @param null|string $name
     * @return Route
     * @throws InvalidRoute
     */
    public function createRoute(string $method, string $definition, $handler, ?string $name = null): Route
    {
        $path = $this->parser->parse($definition);

        $this->validator->validate($path);

        return new Route($method, $path, $handler, $name);
    }
}
