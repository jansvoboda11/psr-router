<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

use Svoboda\Router\Route\Route;

/**
 * Leaf representing the end of a route path.
 */
class Leaf implements TreeNode
{
    /**
     * The route.
     *
     * @var Route
     */
    private $route;

    /**
     * Index of the route in collection.
     *
     * @var int
     */
    private $index;

    /**
     * Constructor.
     *
     * @param Route $route
     * @param int $index
     */
    public function __construct(Route $route, int $index)
    {
        $this->route = $route;
        $this->index = $index;
    }

    /**
     * Returns the route.
     *
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * Returns route index.
     *
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function accept(TreeVisitor $visitor): void
    {
        $visitor->enterLeaf($this);

        $visitor->leaveLeaf($this);
    }

    /**
     * @inheritdoc
     */
    public function addChild(TreeNode $child): void
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function getChildren(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function gatherLeaves(): array
    {
        return [$this];
    }

    /**
     * @inheritdoc
     */
    public function withoutChildren(): TreeNode
    {
        return clone $this;
    }
}
