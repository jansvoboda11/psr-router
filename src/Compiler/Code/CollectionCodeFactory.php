<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Code;

use Svoboda\Router\RouteCollection;

/**
 * Creates PHP code that matches incoming requests against the route collection.
 */
interface CollectionCodeFactory
{
    /**
     * Creates PHP code for the given collection.
     *
     * @param RouteCollection $routes
     * @return string
     */
    public function create(RouteCollection $routes): string;
}
