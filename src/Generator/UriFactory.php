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
     * Create new path URI.
     *
     * @param RoutePath $path
     * @param Types $types
     * @param array $attributes
     * @return PathUri
     * @throws InvalidAttribute
     */
    public function create(RoutePath $path, Types $types, array $attributes = []): PathUri
    {
        return new PathUri($path, $types, $attributes);
    }
}
