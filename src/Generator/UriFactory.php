<?php

declare(strict_types=1);

namespace Svoboda\Router\Generator;

use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Types\Types;

/**
 * Creates path URIs.
 */
class UriFactory
{
    /**
     * Creates new path URI.
     *
     * @param RoutePath $path
     * @param Types $types
     * @param array $attributes
     * @return string
     * @throws InvalidAttribute
     */
    public function create(RoutePath $path, Types $types, array $attributes = []): string
    {
        $uri = new PathUri($path, $types, $attributes);

        return (string)$uri;
    }
}
