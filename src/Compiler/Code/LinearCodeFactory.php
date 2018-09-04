<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Code;

use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;

/**
 * Creates linear PHP code for routes.
 */
class LinearCodeFactory implements RoutesCodeFactory
{
    /**
     * @inheritdoc
     */
    public function create(RouteCollection $routes): string
    {
        $routeCodes = array_map(function (int $index, Route $route): string {
            return (string)(new PathCode($route, $index));
        }, range(0, $routes->count() - 1), $routes->all());

        return implode("", $routeCodes);
    }
}
