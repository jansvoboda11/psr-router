<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\PsrRouter\Match;
use Svoboda\PsrRouter\Route\Route;

/**
 * Iterates over array of individual regular expressions and matches them
 * one-by-one.
 */
class MultiPatternMatcher implements Matcher
{
    /**
     * Array of routes and their regular expressions.
     *
     * @var array
     */
    private $records;

    /**
     * Constructor.
     *
     * @param array $records
     */
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    /**
     * @inheritdoc
     */
    public function match(ServerRequestInterface $request): ?Match
    {
        $requestPath = $request->getMethod() . $request->getUri()->getPath();

        foreach ($this->records as $record) {
            $matches = [];

            [$pattern, $route] = $record;

            if (!preg_match($pattern, $requestPath, $matches)) {
                continue;
            }

            return $this->createResult($route, $request, $matches);
        }

        return null;
    }

    /**
     * Creates a match.
     *
     * @param Route $route
     * @param ServerRequestInterface $request
     * @param array $matches
     * @return Match
     */
    private function createResult(Route $route, ServerRequestInterface $request, array $matches): Match
    {
        $handler = $route->getHandler();
        $attributes = $route->getAttributes();

        foreach ($attributes as $attribute) {
            $name = $attribute->getName();

            $value = $matches[$name] ?? null;

            $request = $request->withAttribute($name, $value);
        }

        return new Match($handler, $request);
    }
}
