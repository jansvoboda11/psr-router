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
     * @param null|Parser $parser
     * @param null|Validator $validator
     */
    public function __construct(?Parser $parser = null, ?Validator $validator = null)
    {
        $this->parser = $parser ?? new Parser();
        $this->validator = $validator ?? new Validator();
    }

    /**
     * Creates new route.
     *
     * @param string $method
     * @param string $definition
     * @param mixed $handler
     * @return Route
     * @throws InvalidRoute
     */
    public function createRoute(string $method, string $definition, $handler)
    {
        $path = $this->parser->parse($definition);

        $this->validator->validate($path);

        return new Route($method, $path, $handler);
    }
}
