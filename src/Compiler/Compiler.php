<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\RouteCollection;

/**
 * Creates a matcher from route collection.
 */
interface Compiler
{
    /**
     * Compiles the route collection into a matcher.
     *
     * @param RouteCollection $routes
     * @return Matcher
     */
    public function compile(RouteCollection $routes): Matcher;
}
