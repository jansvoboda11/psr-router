<?php

declare(strict_types=1);

namespace Svoboda\Router\Generator;

use Svoboda\Router\Route\Path\RoutePath;

/**
 * Creates path URIs.
 */
class UriFactory
{
    /**
     * Creates new path URI.
     *
     * @param RoutePath $path
     * @param array $attributes
     * @return string
     * @throws InvalidAttribute
     */
    public function create(RoutePath $path, array $attributes = []): string
    {
        $uri = new PathUri($path, $attributes);

        return (string)$uri;
    }
}
