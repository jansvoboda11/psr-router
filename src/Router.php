<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\PsrRouter\Compiler\CompilationContext;
use Svoboda\PsrRouter\Compiler\Compiler;
use Svoboda\PsrRouter\Compiler\Matcher;
use Svoboda\PsrRouter\Compiler\NaiveCompiler;

/**
 * Routes an incoming HTTP requests based on given collection of routes.
 */
class Router
{
    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @param RouteCollection $routes
     * @param Compiler $compiler
     * @param CompilationContext $context
     */
    public function __construct(RouteCollection $routes, Compiler $compiler, CompilationContext $context)
    {
        $this->matcher = $compiler->compile($routes, $context);
    }

    /**
     * Creates new router for given routes with default settings.
     *
     * @param RouteCollection $routes
     * @param null|CompilationContext $context
     * @return self
     */
    public static function create(RouteCollection $routes, ?CompilationContext $context = null): self
    {
        $compiler = new NaiveCompiler();
        $context = $context ?? CompilationContext::createDefault();

        return new self($routes, $compiler, $context);
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
