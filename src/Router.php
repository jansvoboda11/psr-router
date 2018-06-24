<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\PsrRouter\Compiler\CompilationContext;
use Svoboda\PsrRouter\Compiler\CompilerInterface;
use Svoboda\PsrRouter\Compiler\NaiveCompiler;
use Svoboda\PsrRouter\Parser\Parser;

/**
 * Routes an incoming HTTP requests based on given collection of routes.
 */
class Router
{
    /**
     * @var Compiler\MatcherInterface
     */
    private $matcher;

    /**
     * @param RouteCollection $routes
     * @param Parser $parser
     * @param CompilerInterface $compiler
     * @param CompilationContext $context
     * @throws InvalidRoute
     */
    public function __construct(
        RouteCollection $routes,
        Parser $parser,
        CompilerInterface $compiler,
        CompilationContext $context
    ) {
        $parsed = [];

        foreach ($routes as $route) {
            $parsed[] = $parser->parse($route);
        }

        $this->matcher = $compiler->compile($parsed, $context);
    }

    /**
     * Creates new router for given routes with default settings.
     *
     * @param RouteCollection $routes
     * @param null|CompilationContext $context
     * @return self
     * @throws InvalidRoute
     */
    public static function create(RouteCollection $routes, ?CompilationContext $context = null): self
    {
        $parser = new Parser();
        $compiler = new NaiveCompiler();
        $context = $context ?? CompilationContext::createDefault();

        return new self($routes, $parser, $compiler, $context);
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
