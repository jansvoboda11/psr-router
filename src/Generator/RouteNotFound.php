<?php

declare(strict_types=1);

namespace Svoboda\Router\Generator;

use Svoboda\Router\Exception;

/**
 * Route not found in the collection.
 */
class RouteNotFound extends Exception
{
    /**
     * Route with given name not found.
     *
     * @param string $name
     * @return RouteNotFound
     */
    public static function named(string $name): self
    {
        return new self("Route with name '$name' does not exist");
    }
}
