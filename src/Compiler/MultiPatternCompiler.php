<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Compiler\Pattern\PathPatternFactory;
use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\Matcher\MultiPatternMatcher;
use Svoboda\Router\RouteCollection;

/**
 * Creates multiple small regular expressions - one for each route.
 */
class MultiPatternCompiler implements Compiler
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
        $records = [];

        foreach ($routes->all() as $route) {
            $path = $route->getPath();

            $pathPattern = $this->patternFactory->create($path);

            $pattern = "#^$pathPattern$#";

            $records[] = [$pattern, $route];
        }

        return new MultiPatternMatcher($records);
    }
}
