<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\RouteCollection;

/**
 * Creates one big regular expression combining all routes.
 */
class SinglePatternCompiler implements Compiler
{
    /**
     * The pattern factory.
     *
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
        $patterns = [];

        $routesArray = $routes->all();

        foreach ($routes->all() as $index => $route) {
            $method = $route->getMethod();
            $path = $route->getPath();

            $pathPattern = $this->patternFactory->create($path);

            $pattern = "$pathPattern{}$method(*MARK:$index)";

            $patterns[] = $pattern;
        }

        $patterns = implode("|", $patterns);

        $pattern = "#^(?|$patterns)$#";

        return new SinglePatternMatcher($pattern, $routesArray);
    }
}
