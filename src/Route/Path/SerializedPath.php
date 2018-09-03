<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

class SerializedPath extends PathVisitor
{
    /**
     * @var RoutePath[]
     */
    private $path;

    /**
     * Constructor.
     *
     * @param RoutePath $path
     */
    public function __construct(RoutePath $path)
    {
        $this->path = [];

        $path->accept($this);
    }

    public function enterAttribute(AttributePath $path): void
    {
        $this->path[] = new AttributePath($path->getName(), $path->getType());
    }

    public function enterOptional(OptionalPath $path): void
    {
        $this->path[] = new OptionalPath(new EmptyPath());
    }

    public function enterStatic(StaticPath $path): void
    {
        $this->path[] = new StaticPath($path->getStatic());
    }

    public function toArray(): array
    {
        return $this->path;
    }
}
