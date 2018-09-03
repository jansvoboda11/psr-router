<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

class PathSerializer
{
    /**
     * @param RoutePath $path
     * @return RoutePath[]
     */
    public function serialize(RoutePath $path): array
    {
        $serialized = new SerializedPath($path);

        return (array)$serialized;
    }
}
