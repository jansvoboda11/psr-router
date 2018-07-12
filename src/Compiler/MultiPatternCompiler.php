<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\RouteCollection;

/**
 * Creates one regular expression per route.
 */
class MultiPatternCompiler implements Compiler
{
    /**
     * @var PatternFactory
     */
    private $patternFactory;

    /**
     * Constructor.
     *
     * @param PatternFactory $patternFactory
     */
    public function __construct(PatternFactory $patternFactory)
    {
        $this->patternFactory = $patternFactory;
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes): Matcher
    {
        $records = [];

        foreach ($routes->all() as $route) {
            $method = $route->getMethod();
            $path = $route->getPath();
            $types = $route->getTypes();

            $pathPattern = $this->patternFactory->create($path, $types);

            $pattern = "#^" . $method . $pathPattern . "$#";

            $records[] = [$pattern, $route];
        }

        return new MultiPatternMatcher($records);
    }
}
