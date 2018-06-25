<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\Semantics\Validator;

class RouteFactory
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param null|Parser $parser
     * @param null|Validator $validator
     */
    public function __construct(?Parser $parser = null, ?Validator $validator = null)
    {
        $this->parser = $parser ?? new Parser();
        $this->validator = $validator ?? new Validator();
    }

    /**
     * Create new route.
     *
     * @param string $method
     * @param string $path
     * @param mixed $handler
     * @return Route
     * @throws InvalidRoute
     */
    public function createRoute(string $method, string $path, $handler)
    {
        $ast = $this->parser->parse($path);

        $this->validator->validate($ast);

        return new Route($method, $ast, $handler);
    }
}
