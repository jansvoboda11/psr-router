<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

use Svoboda\PsrRouter\Route\Route;
use Svoboda\PsrRouter\RouteCollection;

/**
 * Creates one regular expression per route.
 */
class MultiPatternCompiler implements Compiler
{
    /**
     * The regular expression builder.
     *
     * @var PatternBuilder
     */
    private $patternBuilder;

    /**
     * @param null|PatternBuilder $visitor
     */
    public function __construct(?PatternBuilder $visitor = null)
    {
        $this->patternBuilder = $visitor ?? new PatternBuilder();
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes, Context $context): Matcher
    {
        $records = [];

        /** @var Route $route */
        foreach ($routes as $route) {
            $method = $route->getMethod();
            $path = $route->getPath();

            $pathPattern = $this->patternBuilder->buildPattern($path, $context);

            $pattern = "#^" . $method . $pathPattern . "$#";

            $records[] = [$pattern, $route];
        }

        return new MultiPatternMatcher($records);
    }
}
