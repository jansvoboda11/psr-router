<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Path;

use Svoboda\Router\Route\Route;

/**
 * Creates PHP code for a route.
 */
class PathCodeFactory
{
    /**
     * Creates new piece of PHP code.
     *
     * @param Route $route
     * @param int $index
     * @return string
     */
    public function create(Route $route, int $index): string
    {
        $code = new PathCode($route, $index);

        return (string)$code;
    }
}
