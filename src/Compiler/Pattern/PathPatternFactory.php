<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Pattern;

use Svoboda\Router\Route\Path\RoutePath;

/**
 * Creates path patterns.
 */
class PathPatternFactory
{
    /**
     * Creates new path pattern.
     *
     * @param RoutePath $path
     * @return string
     */
    public function create(RoutePath $path): string
    {
        $pattern = new PathPattern($path);

        return (string)$pattern;
    }
}
