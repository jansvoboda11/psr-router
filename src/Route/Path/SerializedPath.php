<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

/**
 * Route path serialized to array (instead of being linked list).
 */
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

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePath $path): void
    {
        $this->path[] = new AttributePath($path->getName(), $path->getType());
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path): void
    {
        $this->path[] = new OptionalPath(new EmptyPath());
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path): void
    {
        $this->path[] = new StaticPath($path->getStatic());
    }

    /**
     * @inheritdoc
     */
    public function enterEmpty(EmptyPath $path): void
    {
        $this->path[] = new EmptyPath();
    }

    /**
     * Returns the serialized path as an array.
     *
     * @return RoutePath[]
     */
    public function toArray(): array
    {
        return $this->path;
    }
}
