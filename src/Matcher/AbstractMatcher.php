<?php

declare(strict_types=1);

namespace Svoboda\Router\Matcher;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Match;
use Svoboda\Router\Route\Route;

/**
 * The base abstract class for all matchers.
 */
abstract class AbstractMatcher implements Matcher
{
    /**
     * Creates a match.
     *
     * @param Route $route
     * @param ServerRequestInterface $request
     * @param string[] $matches
     * @return Match
     */
    protected function createResult(Route $route, ServerRequestInterface $request, array $matches): Match
    {
        $attributes = $route->getAttributes();

        foreach ($attributes as $index => $attribute) {
            $name = $attribute->getName();

            if (array_key_exists($index, $matches)) {
                $request = $request->withAttribute($name, $matches[$index]);
            }
        }

        return new Match($route, $request);
    }
}
