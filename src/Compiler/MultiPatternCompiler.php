<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

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
     * Constructor.
     *
     * @param null|PatternBuilder $patternBuilder
     */
    public function __construct(?PatternBuilder $patternBuilder = null)
    {
        $this->patternBuilder = $patternBuilder ?? new PatternBuilder();
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes, Context $context): Matcher
    {
        $records = [];

        foreach ($routes->all() as $route) {
            $method = $route->getMethod();
            $path = $route->getPath();

            $pathPattern = $this->patternBuilder->buildPattern($path, $context);

            $pattern = "#^" . $method . $pathPattern . "$#";

            $records[] = [$pattern, $route];
        }

        return new MultiPatternMatcher($records);
    }
}
