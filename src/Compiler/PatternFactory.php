<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Route\Path\RoutePath;

/**
 * Creates path patterns.
 */
class PatternFactory
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
