<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Route\Route;

/**
 * Iterates over an array of individual regular expressions and matches them one-by-one.
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
    public function match(ServerRequestInterface $request): Match
    {
        $allowedMethods = [];

        $requestMethod = $request->getMethod();
        $requestPath = $request->getUri()->getPath();

        foreach ($this->records as $record) {
            $matches = [];

            /** @var Route $route */
            [$pattern, $route] = $record;

            $routeMethod = $route->getMethod();

            if (preg_match($pattern, $requestPath, $matches)) {
                if ($requestMethod === $routeMethod) {
                    return $this->createResult($route, $request, $matches);
                }

                if (!array_key_exists($routeMethod, $allowedMethods)) {
                    $allowedMethods[$routeMethod] = $route;
                }
            }
        }

        throw new Failure($allowedMethods, $request);
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
        $attributes = $route->getAttributes();

        foreach ($attributes as $attribute) {
            $name = $attribute->getName();

            if (array_key_exists($name, $matches)) {
                $request = $request->withAttribute($name, $matches[$name]);
            }
        }

        return new Match($route, $request);
    }
}
