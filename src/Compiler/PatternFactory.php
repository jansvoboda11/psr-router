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
     * @return string
     */
    public function create(RoutePath $path, Types $types): string
    {
        $pattern = new PathPattern($path, $types);

        return (string)$pattern;
    }
}
