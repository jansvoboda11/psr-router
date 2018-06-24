<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\PsrRouter\Parser\ParsedRoute;
use Svoboda\PsrRouter\Match;
use function preg_match;

/**
 * Iterates over array of individual regular expressions and matches them one-by-one.
 */
class NaiveMatcher implements MatcherInterface
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @inheritdoc
     */
    public function match(ServerRequestInterface $request): ?Match
    {
        $path = $request->getMethod() . $request->getUri()->getPath();

        foreach ($this->routes as $route) {
            $matches = [];

            [$pattern, $parsed] = $route;

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            return $this->createResult($parsed, $request, $matches);
        }

        return null;
    }

    /**
     * Creates a match.
     *
     * @param ParsedRoute $route
     * @param ServerRequestInterface $request
     * @param array $matches
     * @return Match
     */
    private function createResult(ParsedRoute $route, ServerRequestInterface $request, array $matches): Match
    {
        $handlerName = $route->getHandlerName();

        $attributes = $route->gatherAttributes();

        foreach ($attributes as $name => $info) {
            $value = $matches[$name] ?? null;

            $request = $request->withAttribute($name, $value);
        }

        return new Match($handlerName, $request);
    }
}
