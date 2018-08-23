<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Route\Method;
use Svoboda\Router\Route\Route;

/**
 * Matches one big regular expression.
 */
class SinglePatternMatcher extends AbstractMatcher
{
    /**
     * The pattern.
     *
     * @var string
     */
    private $pattern;

    /**
     * All registered routes.
     *
     * @var Route[]
     */
    private $routes;

    /**
     * Constructor.
     *
     * @param string $pattern
     * @param array $routes
     */
    public function __construct(string $pattern, array $routes)
    {
        $this->pattern = $pattern;
        $this->routes = $routes;
    }

    /**
     * @inheritdoc
     */
    public function match(ServerRequestInterface $request): Match
    {
        $requestMethod = $request->getMethod();
        $requestPath = $request->getUri()->getPath();

        $matches = [];

        $matchThis = "$requestPath{}$requestMethod";

        if (preg_match($this->pattern, $matchThis, $matches) === 1) {
            $route = $this->getRoute($matches);
            $matches = $this->normalize($matches);

            return $this->createResult($route, $request, $matches);
        }

        $allowed = $this->getAllowedMethods($request);

        throw new Failure($allowed, $request);
    }

    /**
     * Get an array of allowed methods and their routes.
     *
     * @param ServerRequestInterface $request
     * @return array
     */
    private function getAllowedMethods(ServerRequestInterface $request): array
    {
        $allowed = [];

        $requestPath = $request->getUri()->getPath();

        foreach (Method::all() as $method) {
            $matchThis = "$requestPath{}$method";

            if (preg_match($this->pattern, $matchThis, $matches) === 1) {
                $allowed[$method] = $this->getRoute($matches);
            }
        }

        return $allowed;
    }

    /**
     * Returns the matched route based on the matches array.
     *
     * @param string[] $matches
     * @return Route
     */
    private function getRoute(array $matches): Route
    {
        $index = intval($matches["MARK"]);

        return $this->routes[$index];
    }

    /**
     * Normalizes the matches array.
     *
     * @param string[] $matches
     * @return string[]
     */
    private function normalize(array $matches): array
    {
        // remove the full match
        array_shift($matches);

        // remove the mark
        unset($matches["MARK"]);

        return $matches;
    }
}
