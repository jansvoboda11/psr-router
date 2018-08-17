<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;

/**
 * The matching callback of a path.
 */
class PathCallback extends PathVisitor
{
    /**
     * The route path.
     *
     * @var RoutePath
     */
    private $path;

    /**
     * The callback array.
     *
     * @var callable[]
     */
    private $callbacks;

    /**
     * Constructor.
     *
     * @param RoutePath $path
     */
    public function __construct(RoutePath $path)
    {
        $this->path = $path;
        $this->callbacks = [];

        // todo: implement
        $this->path->accept($this);
    }

    /**
     * Returns an array with callbacks.
     *
     * @return callable[]
     */
    public function toArray(): array
    {
        return $this->callbacks;
    }
}
