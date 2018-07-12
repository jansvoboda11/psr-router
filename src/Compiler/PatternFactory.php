<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Types\Types;

/**
 * Creates path patterns.
 */
class PatternFactory
{
    /**
     * Creates new path pattern.
     *
     * @param RoutePath $path
     * @param Types $types
     * @return PathPattern
     */
    public function create(RoutePath $path, Types $types): PathPattern
    {
        return new PathPattern($path, $types);
    }
}
