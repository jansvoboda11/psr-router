<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Compiler\Path\PathPatternFactory;
use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\Matcher\SinglePatternMatcher;
use Svoboda\Router\RouteCollection;

/**
 * Creates one big regular expression combining all routes.
 */
class SinglePatternCompiler implements Compiler
{
    /**
     * The pattern factory.
     *
     * @var PathPatternFactory
     */
    private $patternFactory;

    /**
     * Constructor.
     *
     * @param PathPatternFactory $patternFactory
     */
    public function __construct(PathPatternFactory $patternFactory)
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

        foreach ($routesArray as $index => $route) {
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
