<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

use Svoboda\Router\Route\Route;

/**
 * Node representing the end of a route path.
 */
class LeafNode implements TreeNode
{
    /**
     * The route.
     *
     * @var Route
     */
    private $route;

    /**
     * Constructor.
     *
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
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
    public function equals(TreeNode $node): bool
    {
        return $node instanceof self
            && $this->route == $node->route;
    }
}
