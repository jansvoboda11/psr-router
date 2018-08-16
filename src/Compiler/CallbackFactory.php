<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Route\Path\RoutePath;

/**
 * Creates path callbacks.
 */
class CallbackFactory
{
    /**
     * Creates new path callback.
     *
     * @param RoutePath $path
     * @return callback[]
     */
    public function create(RoutePath $path): array
    {
        $callback = new PathCallback($path);

        return $callback->toArray();
    }
}
