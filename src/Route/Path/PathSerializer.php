<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

/**
 * Transforms the route path from linked list to a flat array.
 */
class PathSerializer
{
    /**
     * Serializes the route path to array.
     *
     * @param RoutePath $path
     * @return RoutePath[]
     */
    public function serialize(RoutePath $path): array
    {
        $serialized = new SerializedPath($path);

        return $serialized->toArray();
    }
}
