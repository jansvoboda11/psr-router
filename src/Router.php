<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Compiler\Compiler;
use Svoboda\Router\Compiler\Matcher;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\PatternFactory;

/**
 * Routes an incoming HTTP requests based on given collection of routes.
 */
class Router
{
    /**
     * The request matcher.
     *
     * @var Matcher
     */
    private $matcher;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes
     * @param Compiler $compiler
     */
    public function __construct(RouteCollection $routes, Compiler $compiler)
    {
        $this->matcher = $compiler->compile($routes);
    }

    /**
     * Creates new router for given routes.
     *
     * @param RouteCollection $routes
     * @return Router
     */
    public static function create(RouteCollection $routes): self
    {
        $factory = new PatternFactory();
        $compiler = new MultiPatternCompiler($factory);

        return new self($routes, $compiler);
    }

    /**
     * Match the incoming HTTP request.
     *
     * @param ServerRequestInterface $request
     * @return Match
     * @throws NoMatch
     */
    public function match(ServerRequestInterface $request): Match
    {
        return $this->matcher->match($request);
    }
}
