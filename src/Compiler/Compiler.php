<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

use Svoboda\PsrRouter\RouteCollection;

/**
 * Creates a matcher from parsed routes.
 */
interface Compiler
{
    /**
     * Compiles the parsed routes into a matcher in the given context.
     *
     * @param RouteCollection $routes
     * @param CompilationContext $context
     * @return Matcher
     */
    public function compile(RouteCollection $routes, CompilationContext $context): Matcher;
}
