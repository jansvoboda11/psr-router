<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;
use Svoboda\PsrRouter\Parser\ParsedRoute;

/**
 * Creates a matcher from parsed routes.
 */
interface CompilerInterface
{
    /**
     * Compiles the parsed routes into a matcher in the given context.
     *
     * @param ParsedRoute[] $routes
     * @param CompilationContext $context
     * @return MatcherInterface
     */
    public function compile(array $routes, CompilationContext $context): MatcherInterface;
}
