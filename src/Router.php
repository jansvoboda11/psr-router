<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\PsrRouter\Compiler\Context;
use Svoboda\PsrRouter\Compiler\Compiler;
use Svoboda\PsrRouter\Compiler\Matcher;
use Svoboda\PsrRouter\Compiler\MultiPatternCompiler;
use Svoboda\PsrRouter\Compiler\PatternBuilder;

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
     * Creates new router for given routes with default settings.
     *
     * @param RouteCollection $routes
     * @param null|Context $context
     * @return self
     */
    public static function create(RouteCollection $routes, ?Context $context = null): self
    {
        $context = $context ?? Context::createDefault();

        $builder = new PatternBuilder($context);
        $compiler = new MultiPatternCompiler($builder);

        return new self($routes, $compiler);
    }

    /**
     * Match the incoming HTTP request.
     *
     * @param ServerRequestInterface $request
     * @return null|Match
     */
    public function match(ServerRequestInterface $request): ?Match
    {
        return $this->matcher->match($request);
    }
}
